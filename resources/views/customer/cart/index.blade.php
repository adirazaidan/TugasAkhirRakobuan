@extends('layouts.customer')

@section('title', 'Keranjang Anda')

@section('content')
<div class="cart-page-container">
    <div class="cart-content-wrapper">
        <div class="cart-header">
            <a href="{{ route('customer.menu.index') }}" class="back-button" title="Kembali ke Menu"><i class="fas fa-arrow-left"></i></a>
            <h1>Keranjang Pesanan</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="customer-details-box">
            Pesanan untuk <strong>{{ session('customer_name') }}</strong> di Meja <strong>{{ session('table_number') }}</strong>
        </div>

        <div id="cart-items-list" class="cart-items-list">
            @forelse ($cart as $id => $details)
                <div class="cart-item-card-new" id="cart-item-{{ $id }}" data-id="{{ $id }}" data-price="{{ $details['price'] }}" data-stock="{{ $details['product']->stock }}">
                    <div class="cart-item-image-wrapper">
                        <img src="{{ $details['product']->image ? Storage::url('products/' . $details['product']->image) : 'https://via.placeholder.com/150' }}" alt="{{ $details['name'] }}" class="cart-item-image">
                        <button class="zoom-btn card-zoom-btn" title="Perbesar gambar" data-image-url="{{ $details['product']->image ? Storage::url('products/' . $details['product']->image) : 'https://via.placeholder.com/400' }}">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                    <div class="cart-item-details">
                        <div class="item-info-header">
                            <h4 class="item-name">{{ $details['name'] }}</h4>
                            <strong class="item-subtotal">Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</strong>
                        </div>
                        
                        <div class="item-note-display">
                            <i class="fas fa-sticky-note"></i>
                            <span class="item-notes-text">{{ $details['notes'] ?: 'Tidak Ada Catatan' }}</span>
                        </div>
                        <div class="form-check-wrapper">
                            <div class="form-check">
                                <input class="form-check-input bungkus-checkbox" type="checkbox" value="1" id="bungkus-{{ $id }}" 
                                    data-product-id="{{ $id }}"
                                    @if(str_contains($details['notes'] ?? '', '(Bungkus)')) checked @endif>
                                <label class="form-check-label" for="bungkus-{{ $id }}">
                                    Bungkus (Takeaway)
                                </label>
                            </div>
                        </div>
                        <div class="item-controls-cart">
                            <div class="product-controls">
                                <div class="cart-action-wrapper">
                                    <div class="quantity-selector-inline" data-product-id="{{ $id }}">
                                        <button class="btn-quantity-inline btn-decrease-inline">-</button>
                                        <span class="quantity-inline-display">{{ $details['quantity'] }}</span>
                                        <button class="btn-quantity-inline btn-increase-inline" @if($details['quantity'] >= $details['product']->stock) disabled @endif>+</button>
                                    </div>
                                </div>
                                <button class="btn-edit-notes has-notes" 
                                        data-product-id="{{ $id }}" 
                                        data-notes="{{ $details['notes'] ?? '' }}">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                            </div>
                            <div class="inline-stock-feedback"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-cart" id="empty-cart-message">
                    <i class="fas fa-shopping-cart fa-3x"></i>
                    <p>Keranjang Anda masih kosong.</p>
                    <a href="{{ route('customer.menu.index') }}" class="btn-primary">Lihat Menu</a>
                </div>
            @endforelse
        </div>
    </div>
    
@if (count($cart) > 0)
    {{-- Menggunakan struktur dan class yang terinspirasi dari status-page-actions --}}
    <div class="cart-summary-sticky">
        
        {{-- Bagian Kiri: Total Harga (dibuat agar terlihat seperti tombol info) --}}
        <div class="summary-info">
            <span>Total Harga</span>
            <strong id="grand-total">Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
        </div>

        {{-- Bagian Kanan: Tombol Aksi Checkout --}}
        <div class="summary-action">
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-checkout">
                    Kirim Orderan <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
        
    </div>
@endif
</div>
@endsection