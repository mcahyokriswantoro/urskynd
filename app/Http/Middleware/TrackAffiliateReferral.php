<?php

namespace App\Http\Middleware;

use App\Models\AffiliateClick;
use App\Models\Product;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TrackAffiliateReferral
{
    public const COOKIE_NAME = 'urskynd_aff_ref';

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && $request->filled('ref')) {
            $refCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $request->query('ref')));

            if ($refCode && Schema::hasTable('users') && Schema::hasColumn('users', 'affiliate_code')) {
                $affiliate = User::where('is_affiliate', true)
                    ->where('affiliate_status', 'active')
                    ->where('affiliate_code', $refCode)
                    ->first();

                if ($affiliate) {
                    $request->session()->put('affiliate_ref', $affiliate->affiliate_code);
                    Cookie::queue(self::COOKIE_NAME, $affiliate->affiliate_code, 60 * 24 * 30);

                    // Deteksi produk yang sedang dibuka dari URL /products/{slug} atau ?product=id
                    $productId = null;
                    $routeProduct = $request->route('product');
                    if ($routeProduct instanceof Product) {
                        $productId = $routeProduct->id;
                    } elseif (is_string($routeProduct) || is_numeric($routeProduct)) {
                        $foundProduct = Product::where('id', $routeProduct)->orWhere('slug', $routeProduct)->first();
                        $productId = $foundProduct?->id;
                    } elseif ($request->filled('product')) {
                        $foundProduct = Product::where('id', $request->query('product'))->orWhere('slug', $request->query('product'))->first();
                        $productId = $foundProduct?->id;
                    }

                    if ($productId) {
                        $request->session()->put('affiliate_last_product_id', $productId);
                    }

                    // Catat klik unik per sesi per produk (seperti Shopee Affiliate Click Tracker)
                    $clickKey = 'aff_clicked_' . $affiliate->id . '_' . ($productId ?? 'general');
                    if (!$request->session()->has($clickKey) && Schema::hasTable('affiliate_clicks')) {
                        AffiliateClick::create([
                            'affiliate_id' => $affiliate->id,
                            'product_id' => $productId,
                            'ip_address' => $request->ip(),
                            'user_agent' => substr((string) $request->userAgent(), 0, 250),
                            'target_path' => '/' . ltrim($request->path(), '/'),
                        ]);
                        $request->session()->put($clickKey, true);
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Ambil kode afiliasi aktif dari input, session, atau cookie 30 hari.
     */
    public static function currentCode(Request $request, ?string $explicitCode = null): ?string
    {
        $raw = $explicitCode
            ?: $request->query('ref')
            ?: $request->session()->get('affiliate_ref')
            ?: $request->cookie(self::COOKIE_NAME);

        return $raw ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $raw)) : null;
    }

    /**
     * Ambil objek User Mitra Afiliator yang terdeteksi otomatis.
     */
    public static function currentAffiliate(Request $request, ?string $explicitCode = null): ?User
    {
        $code = self::currentCode($request, $explicitCode);
        if (!$code) {
            return null;
        }

        return User::where('is_affiliate', true)
            ->where('affiliate_status', 'active')
            ->where('affiliate_code', $code)
            ->first();
    }
}

