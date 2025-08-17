<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Http;

class TryOnController extends Controller
{
    public function showForm(Request $request)
    {
        $productData = null;
        
        // Kiểm tra nếu có product_id từ query string
        if ($request->has('product_id')) {
            $productId = $request->get('product_id');
            $product = \App\Models\Products::with('images')->find($productId);
            
            if ($product) {
                $productData = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->images->first() ? asset($product->images->first()->path) : null,
                    'category' => $product->category->name ?? 'clothing'
                ];
            }
        }
        
        return view('tryon.form', compact('productData'));
    }
    public function process(Request $request)
    {
        // Handle product image from URL (when user clicks "Try On" from product page)
        $clothImagePath = null;
        if ($request->has('product_image_url') && !$request->hasFile('cloth_image')) {
            // Download product image from URL
            try {
                $productImageUrl = $request->input('product_image_url');
                $imageContents = file_get_contents($productImageUrl);
                
                if ($imageContents !== false) {
                    $fileName = 'product_' . time() . '_' . uniqid() . '.jpg';
                    $clothImagePath = 'uploads/' . $fileName;
                    Storage::disk('public')->put($clothImagePath, $imageContents);
                }
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Không thể tải ảnh sản phẩm. Vui lòng upload ảnh thủ công.'])->withInput();
            }
        }

        $request->validate([
            'person_image' => 'required|image|max:10240', // 10MB max
            'cloth_image' => $clothImagePath ? 'nullable' : 'required|image|max:10240',
            'instructions' => 'nullable|string|max:1000',
            'model_type' => 'nullable|string|in:top,bottom,full',
            'gender' => 'nullable|string|in:male,female,unisex',
            'garment_type' => 'nullable|string|in:shirt,pants,jacket,dress,tshirt',
            'style' => 'nullable|string|in:casual,formal,streetwear,traditional,sports',
        ]);

        try {
            // Store uploaded images
            $personPath = $request->file('person_image')->store('uploads', 'public');
            
            if (!$clothImagePath) {
                $clothPath = $request->file('cloth_image')->store('uploads', 'public');
            } else {
                $clothPath = $clothImagePath;
            }

            $personFull = storage_path("app/public/{$personPath}");
            $clothFull = storage_path("app/public/{$clothPath}");

            // Prepare parameters
            $instructions = $request->input('instructions', '');
            $modelType = $request->input('model_type', '');
            $gender = $request->input('gender', '');
            $garmentType = $request->input('garment_type', '');
            $style = $request->input('style', '');

            // Call Python API
            $client = new Client([
                'timeout' => 120, // 2 minutes timeout
                'connect_timeout' => 30
            ]);

            $response = $client->post('http://localhost:8000/api/try-on', [
                'multipart' => [
                    [
                        'name' => 'person_image',
                        'contents' => fopen($personFull, 'r'),
                        'filename' => basename($personFull),
                    ],
                    [
                        'name' => 'cloth_image',
                        'contents' => fopen($clothFull, 'r'),
                        'filename' => basename($clothFull),
                    ],
                    [
                        'name' => 'instructions',
                        'contents' => $instructions,
                    ],
                    [
                        'name' => 'model_type',
                        'contents' => $modelType,
                    ],
                    [
                        'name' => 'gender',
                        'contents' => $gender,
                    ],
                    [
                        'name' => 'garment_type',
                        'contents' => $garmentType,
                    ],
                    [
                        'name' => 'style',
                        'contents' => $style,
                    ],
                ],
            ]);

            $result = json_decode($response->getBody(), true);

            $resultImage = $result['image'] ?? null;
            $description = $result['text'] ?? 'Kết quả thử đồ ảo thành công';

            // Store result in session for display
            session([
                'tryon_result' => [
                    'person_image' => $personPath,
                    'cloth_image' => $clothPath,
                    'result_image' => $resultImage,
                    'description' => $description,
                ]
            ]);

            // Handle AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thử đồ ảo thành công',
                    'data' => [
                        'result_image' => $resultImage,
                        'description' => $description,
                        'person_image' => $personPath,
                    ],
                    'redirect' => route('tryon.result')
                ]);
            }

            // Regular form submission - redirect to result page
            return redirect()->route('tryon.result');

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $errorMessage = 'Không thể kết nối đến service AI. Vui lòng thử lại sau.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
            
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorMessage = 'Service AI đang bận. Vui lòng thử lại sau.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
            
        } catch (\Exception $e) {
            $errorMessage = 'Có lỗi xảy ra: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }

    public function showResult()
    {
        $result = session('tryon_result');
        
        if (!$result) {
            return redirect()->route('tryon.form')->with('error', 'Không tìm thấy kết quả thử đồ.');
        }

        return view('tryon.form', [
            'personImage' => $result['person_image'],
            'clothImage' => $result['cloth_image'],
            'resultImage' => $result['result_image'],
            'description' => $result['description'],
        ]);
    }



}
