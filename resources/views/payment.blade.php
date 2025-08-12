@extends('app')

@section('body')
    <div class="tt-container">
        <form action="{{route('payment.store')}}" method="post" class="tt-container">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <input type="hidden" name="iduser" value="">
            @php
                $checkoutData = session('checkout_data', []);
                $cartDetails = $checkoutData['cartDetails'] ?? [];
                $subtotal = $checkoutData['subtotal'] ?? 0;
                $voucherDiscount = $checkoutData['voucherDiscount'] ?? 0;
                $shippingFee = $checkoutData['shippingFee'] ?? 0;
                $total = $checkoutData['total'] ?? 0;

            @endphp
            {{-- <input type="text" name="address" value="{{ Auth::user()->addresses->first()->address ?? '' }}" required>
            --}}


            <h1 class="tt-heading-1">Thanh Toán</h1>
            <div class="tt-checkout-container">
                <section class="tt-checkout-form">
                    <h2 class="tt-heading-2">Thông tin giao hàng</h2>

                    <div class="tt-form-group">
                        <label for="fullname" class="tt-label">Họ và tên</label>
                        <input type="text" id="fullname"
                            value="{{ old('fullname', Auth::check() ? Auth::user()->name : '') }}" name="fullname"
                            class="tt-input" {{Auth::check() ? 'disabled' : ''}}  required>
                    </div>

                    <div class="tt-form-row">
                        <div class="tt-form-group">
                            <label for="email" class="tt-label">Email</label>
                            <input type="email" id="email" name="email" class="tt-input"
                                value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" {{Auth::check() ? 'disabled' : ''}} required>
                        </div>

                        <div class="tt-form-group">
                            <label for="phone" class="tt-label">Số điện thoại</label>
                            <input type="tel" id="phone" name="phone" class="tt-input"
                                value="{{ old('phone', Auth::check() ? Auth::user()->phone : '') }}"  {{ Auth::check() && Auth::user()->phone ? 'disabled' : '' }} required>
                        </div>
                    </div>


                    {{-- địa chỉ --}}
                    @if(!Auth::check())
                        <div class="tt-form-row">
                            <div class="tt-form-group">
                                <label for="city" class="tt-label">Thành phố</label>
                                <select id="city" class="tt-select" required>
                                    <option value="">Chọn thành phố</option>
                                </select>
                            </div>
                            <div class="tt-form-group">
                                <label for="district" class="tt-label">Quận/Huyện</label>
                                <select id="district" class="tt-select" required>
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                            </div>
                            <div class="tt-form-group">
                                <label for="ward" class="tt-label">Phường/Xã</label>
                                <select id="ward" class="tt-select" required>
                                    <option value="">Chọn phường/xã</option>
                                </select>
                            </div>
                        </div>
                    @else
                    @endif


                    <div class="tt-form-group">
                        <label for="address" class="tt-label">Địa chỉ</label>
                        {{-- <input type="text" id="address" class="tt-input" required> --}}

                        @if(Auth::check())
                            <select id="address" name="address" class="tt-input" required>
                                @forelse($address as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->address }}, {{ $item->ward }}, {{ $item->district }}, {{ $item->province }}
                                    </option>
                                @empty
                                    <option value="">Chưa có địa chỉ, vui lòng thêm mới</option>
                                @endforelse
                            </select>
                            <a href="{{route('infouser')}}">Thêm địa chỉ mới</a>
                                <div class="alert alert-warning">
                                    <strong>Lưu ý:</strong>Vui lòng cập nhật địa chỉ chính xác để thanh toán!
                                    để thanh toán.
                                </div>

                        @else
                            <input type="text" id="address" name="address" class="tt-input" required>

                        @endif
                    </div>

                    <div class="tt-form-group">
                        <label for="note" class="tt-label">Ghi chú (tuỳ chọn)</label>
                        <textarea id="note" name="note" rows="3" class="tt-textarea"></textarea>
                    </div>

                    <h3 class="tt-heading-3">Phương thức thanh toán</h3>

                    <div class="tt-payment-method">
                        <div class="tt-payment-option">
                            <input type="radio" id="cod" name="payment" class="tt-radio" value="COD" checked>
                            <label for="cod">Thanh toán khi nhận hàng (COD)</label>
                        </div>

                        <div class="tt-payment-option">
                            <input type="radio" id="banking" name="payment" class="tt-radio" value="Banking">
                            <label for="banking">Chuyển khoản ngân hàng</label>
                        </div>

                        <div class="tt-payment-option">
                            <input type="radio" id="momo" name="payment" class="tt-radio" value="Momo">
                            <label for="momo">Ví điện tử MoMo</label>
                        </div>
                    </div>
                </section>
                <section class="tt-order-summary">
                    <h2 class="tt-heading-2">Đơn hàng của bạn</h2>
                    <input type="hidden" name="idproduct" value="">
                    <input type="hidden" name="sl" value="">
                    <input type="hidden" name="kq" value="">

                    @foreach($cartDetails as $item)
                        <div class="tt-product-item">
                            <img src="{{ $item->productVariant->product->thumbnail->path ?? '/img/default.jpg' }}" alt="" class="tt-product-image">
                            <div class="tt-product-info">
                                <div class="tt-product-title">{{ $item->productVariant->product->name ?? 'Sản phẩm không xác định' }}</div>
                                <div class="tt-product-variant">
                                    @if($item->productVariant->size)
                                        Size: {{ $item->productVariant->size->name }} | 
                                    @endif
                                    @if($item->productVariant->color)
                                        Màu: {{ $item->productVariant->color->name }}
                                    @endif
                                </div>
                                <div class="tt-product-variant">Số lượng: {{ $item->quantity }}</div>
                                <div class="tt-product-price">{{ number_format($item->subtotal) }}đ</div>
                            </div>
                        </div>
                    @endforeach



                    <div class="tt-summary-row">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($subtotal) }}đ</span>
                    </div>

                    <div class="tt-summary-row">
                        <span>Phí vận chuyển:</span>
                        <span>{{ number_format($shippingFee) }}đ</span>
                    </div>
                    <div class="tt-summary-row">
                        <span>Giảm giá:</span>
                        <span>{{ number_format($voucherDiscount) }}đ</span>
                    </div>

                    <div class="tt-summary-row tt-total-row">
                        <span>Tổng cộng:</span>
                        <span>{{ number_format($total) }}đ</span>
                    </div>
                    @if (session('error'))
                        <div class="alert alert-danger" style="margin: 10px 0; color: red;">{{ session('error') }}</div>
                    @endif
                    <button type="submit" class="tt-checkout-btn">HOÀN TẤT ĐƠN HÀNG</button>

                    <div class="tt-terms">
                        <p>
                            Bằng cách nhấn "Hoàn tất đơn hàng", bạn đồng ý với
                            <a href="#" class="tt-terms-link">Điều khoản và Điều kiện</a> của chúng tôi.
                        </p>
                    </div>
                </section>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const citySelect = document.getElementById('city');
                const districtSelect = document.getElementById('district');
                const wardSelect = document.getElementById('ward');

                citySelect.name = 'city';
                districtSelect.name = 'district';
                wardSelect.name = 'ward';

                fetch('https://vn-public-apis.fpo.vn/provinces/getAll?limit=-1')
                    .then(response => response.json())
                    .then(data => {
                        const provinces = data.data.data;
                        if (Array.isArray(provinces)) {
                            provinces.forEach(province => {
                                const option = document.createElement('option');
                                option.value = province.code;
                                option.text = province.name_with_type;
                                citySelect.appendChild(option);
                            });

                            @if (!Auth::check() && isset($address) && $address->isNotEmpty())  
                                @php
                                    $defaultAddress = $address->firstWhere('is_default', true) ?? $address->first();
                                @endphp
                                citySelect.value = '{{ $address->province }}';
                                districtSelect.value = '{{ $address->district }}';
                                wardSelect.value = '{{ $address->ward }}';

                                citySelect.dispatchEvent(new Event('change'));
                                setTimeout(() => {
                                    districtSelect.value = '{{ $address->district }}';
                                    districtSelect.dispatchEvent(new Event('change'));
                                    setTimeout(() => {
                                        wardSelect.value = '{{ $address->ward }}';
                                    }, 500);
                                }, 500);
                            @else
                                console.warn("Không có địa chỉ hợp lệ hoặc người dùng chưa đăng nhập.");
                            @endif
                                                        } else {
                            console.error('Expected an array but got:', provinces);
                        }
                    })
                    .catch(error => console.error('Lỗi khi lấy tỉnh:', error));
                // Sự kiện khi chọn tỉnh
                citySelect.addEventListener('change', function () {
                    const provinceCode = this.value;
                    districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                    if (provinceCode) {
                        fetch(`https://vn-public-apis.fpo.vn/districts/getByProvince?provinceCode=${provinceCode}&limit=-1`)
                            .then(response => response.json())
                            .then(data => {
                                const district = data.data.data;

                                district.forEach(district => {
                                    const option = document.createElement('option');
                                    option.value = district.code;
                                    option.text = district.name;
                                    districtSelect.add(option);
                                });
                            })
                            .catch(error => console.error('Lỗi khi lấy huyện:', error));
                    }
                });

                // Sự kiện khi chọn huyện
                districtSelect.addEventListener('change', function () {
                    const districtCode = this.value;
                    if (districtCode) {
                        fetch(`https://vn-public-apis.fpo.vn/wards/getByDistrict?districtCode=${districtCode}&limit=-1`)
                            .then(response => response.json())
                            .then(data => {
                                const ward = data.data.data;

                                ward.forEach(ward => {
                                    const option = document.createElement('option');
                                    option.value = ward.code;
                                    option.text = ward.name;
                                    wardSelect.add(option);
                                });
                            })
                            .catch(error => console.error('Lỗi khi lấy xã:', error));
                    }
                });
            });

        </script>

@endsection