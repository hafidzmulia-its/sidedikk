<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_route_and_required_icon_assets_are_available(): void
    {
        $response = $this->get(route('pwa.manifest'));
        $manifestContents = file_get_contents(public_path('manifest.webmanifest'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/manifest+json');
        $this->assertStringContainsString('SIDEDIKK', $manifestContents);
        $this->assertStringContainsString('"id": "/"', $manifestContents);
        $this->assertStringContainsString('"theme_color": "#95409E"', $manifestContents);
        $this->assertStringContainsString('/brand/icon-192.png', $manifestContents);
        $this->assertStringContainsString('/brand/icon-512.png', $manifestContents);

        $this->assertFileExists(public_path('brand/icon-192.png'));
        $this->assertFileExists(public_path('brand/icon-512.png'));
    }

    public function test_install_page_is_available_with_install_and_open_actions(): void
    {
        $response = $this->get(route('pwa.install'));

        $response->assertOk();
        $response->assertSee('Pasang SIDEDIKK');
        $response->assertSee('Pasang Sekarang');
        $response->assertSee('Panduan Android');
        $response->assertSee('Panduan iPhone &amp; iPad', false);
    }

    public function test_offline_page_and_service_worker_are_available_and_sensitive_routes_are_excluded_from_cache(): void
    {
        $offlineContents = file_get_contents(public_path('offline.html'));
        $serviceWorkerContents = file_get_contents(public_path('sw.js'));

        $this->get('/offline.html')
            ->assertOk();

        $serviceWorker = $this->get(route('pwa.service-worker'));

        $serviceWorker->assertOk();
        $this->assertStringContainsString('SIDEDIKK Offline', $offlineContents);
        $this->assertStringContainsString('/dashboard', $serviceWorkerContents);
        $this->assertStringContainsString('/admin', $serviceWorkerContents);
        $this->assertStringContainsString('/screenings', $serviceWorkerContents);
        $this->assertStringContainsString('/history', $serviceWorkerContents);
        $this->assertStringContainsString('/profile', $serviceWorkerContents);
        $this->assertStringContainsString('/education', $serviceWorkerContents);
        $this->assertStringContainsString('/offline.html', $serviceWorkerContents);
    }
}
