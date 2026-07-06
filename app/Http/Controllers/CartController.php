<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    //  PUBLIC ACTIONS
    // ─────────────────────────────────────────────────────────────────

    public function addToCart($id)
    {
        $product = Product::findOrFail($id);
        $cart = Session::get("cart", []);

        if (isset($cart[$id])) {
            $cart[$id]["quantity"]++;
        } else {
            $cart[$id] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image,
            ];
        }

        Session::put("cart", $cart);
        $this->syncToDb($cart);

        return response()->json([
            "message" => "Produk berhasil ditambahkan ke keranjang!",
            "cart_count" => count($cart),
        ]);
    }

    public function removeFromCart($id)
    {
        $cart = Session::get("cart", []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put("cart", $cart);
            $this->syncToDb($cart);
        }

        return response()->json([
            "message" => "Produk berhasil dihapus!",
            "cart_count" => count($cart),
        ]);
    }

    public function updateCart(Request $request)
    {
        $cart = Session::get("cart", []);
        $id = $request->id;
        $quantity = (int) $request->quantity;

        if (isset($cart[$id]) && $quantity > 0) {
            $cart[$id]["quantity"] = $quantity;
            Session::put("cart", $cart);
            $this->syncToDb($cart);
        }

        return response()->json([
            "message" => "Keranjang diperbarui!",
            "cart_total" => number_format(
                $this->getCartTotal($cart),
                0,
                ",",
                ".",
            ),
            "cart_count" => array_sum(array_column($cart, "quantity")),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  STATIC HELPERS (dipakai AuthenticatedSessionController)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Muat cart dari DB dan simpan ke session.
     * Dipanggil setelah login agar cart tersimpan kembali.
     * Jika ada guest cart di session, gabungkan (guest cart ditambahkan ke DB cart).
     */
    public static function hydrateFromDb(
        int $userId,
        array $guestCart = [],
    ): void {
        // Ambil cart dari DB beserta info produk
        $dbItems = Cart::with("product")->where("user_id", $userId)->get();

        // Bangun cart dari DB
        $merged = [];
        foreach ($dbItems as $item) {
            if (!$item->product) {
                continue;
            }
            $merged[$item->product_id] = [
                "id" => $item->product->id,
                "name" => $item->product->name,
                "quantity" => $item->quantity,
                "price" => $item->product->price,
                "image" => $item->product->image,
            ];
        }

        // Gabungkan dengan guest cart: item baru ditambahkan, qty dijumlahkan
        foreach ($guestCart as $productId => $guestItem) {
            if (isset($merged[$productId])) {
                $merged[$productId]["quantity"] += $guestItem["quantity"];
            } else {
                $merged[$productId] = $guestItem;
            }
        }

        // Simpan hasil gabungan ke DB
        foreach ($merged as $productId => $item) {
            Cart::updateOrCreate(
                ["user_id" => $userId, "product_id" => $productId],
                ["quantity" => $item["quantity"]],
            );
        }

        // Hapus item DB yang sudah tidak ada di merged (karena produk dihapus, dll.)
        $validProductIds = array_keys($merged);
        Cart::where("user_id", $userId)
            ->when(
                !empty($validProductIds),
                fn($q) => $q->whereNotIn("product_id", $validProductIds),
            )
            ->when(empty($validProductIds), fn($q) => $q)
            ->delete();

        Session::put("cart", $merged);
    }

    /**
     * Hapus cart user dari DB dan session.
     * Dipanggil setelah checkout berhasil.
     */
    public static function clearDbCart(int $userId): void
    {
        Cart::where("user_id", $userId)->delete();
    }

    // ─────────────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Sinkronkan session cart ke DB.
     * Hanya berjalan jika user sudah login.
     */
    private function syncToDb(array $cart): void
    {
        if (!Auth::check()) {
            return;
        }

        $userId = Auth::id();
        $cartProductIds = array_keys($cart);

        // Upsert setiap item
        foreach ($cart as $productId => $item) {
            Cart::updateOrCreate(
                ["user_id" => $userId, "product_id" => $productId],
                ["quantity" => $item["quantity"]],
            );
        }

        // Hapus baris DB yang tidak ada di session cart (item dihapus)
        Cart::where("user_id", $userId)
            ->when(
                !empty($cartProductIds),
                fn($q) => $q->whereNotIn("product_id", $cartProductIds),
            )
            ->when(
                empty($cartProductIds),
                fn($q) => $q, // hapus semua jika cart kosong
            )
            ->delete();
    }

    private function getCartTotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item["price"] * $item["quantity"];
        }
        return $total;
    }
}
