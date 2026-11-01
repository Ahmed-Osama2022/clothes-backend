<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutEndpointTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();

    $this->seed(PageSeeder::class);
  }

  /**
   * The English payload exposes every block group with the expected shape.
   */
  public function test_about_returns_the_english_payload(): void
  {
    $response = $this->getJson('/api/v1/about?lang=en');

    $response->assertOk()
      ->assertJsonPath('data.slug', 'about')
      ->assertJsonPath('data.title', 'About Us')
      ->assertJsonCount(3, 'data.sections')
      ->assertJsonCount(3, 'data.stats')
      ->assertJsonCount(3, 'data.contact')
      ->assertJsonCount(3, 'data.social')
      ->assertJsonCount(2, 'data.images')
      ->assertJsonPath('data.sections.0.heading', 'Our Story')
      ->assertJsonPath('data.stats.0.value', '12k+')
      ->assertJsonPath('data.social.0.platform', 'Instagram')
      ->assertJsonStructure([
        'data' => [
          'slug',
          'title',
          'intro',
          'sections' => [['heading', 'body']],
          'stats' => [['label', 'value']],
          'contact' => [['label', 'value']],
          'social' => [['platform', 'url']],
          'images' => [['id', 'url', 'sortOrder', 'createdAt', 'updatedAt']],
          'updatedAt',
        ],
      ]);

    $this->assertNotNull($response->json('data.updatedAt'));
  }

  /**
   * ?lang=ar switches the localized columns; the block structure stays the same.
   */
  public function test_about_returns_the_arabic_payload(): void
  {
    $response = $this->getJson('/api/v1/about?lang=ar');

    $response->assertOk()
      ->assertJsonPath('data.title', 'من نحن')
      ->assertJsonPath('data.sections.0.heading', 'قصتنا')
      ->assertJsonPath('data.stats.0.label', 'عميل سعيد')
      ->assertJsonPath('data.social.0.platform', 'إنستغرام')
      ->assertJsonCount(3, 'data.sections');
  }

  /**
   * Unsupported locales fall back to the application default (config/app.php).
   */
  public function test_about_falls_back_to_the_default_locale_for_unknown_lang(): void
  {
    $response = $this->getJson('/api/v1/about?lang=fr');

    $response->assertOk()
      ->assertJsonPath(
        'data.title',
        config('app.locale') === 'ar' ? 'من نحن' : 'About Us'
      );
  }

  /**
   * An unpublished page must not leak through the API; the body follows the
   * project-wide error shape (message + status).
   */
  public function test_about_returns_a_json_404_when_the_page_is_unpublished(): void
  {
    Page::query()->update(['is_published' => false]);

    $this->getJson('/api/v1/about')
      ->assertStatus(404)
      ->assertExactJson([
        'message' => 'About page not found, try again later or contact support.',
        'status' => 404,
      ]);
  }

  /**
   * Same contract when the row is missing entirely.
   */
  public function test_about_returns_a_json_404_when_the_page_is_missing(): void
  {
    Page::query()->delete();

    $this->getJson('/api/v1/about')->assertStatus(404);
  }

  /**
   * The response is publicly cacheable and revalidates with 304 while the
   * content is unchanged.
   */
  public function test_about_sends_cache_headers_and_revalidates_with_etag(): void
  {
    $first = $this->getJson('/api/v1/about?lang=en');

    $first->assertOk();
    $this->assertStringContainsString('max-age=300', $first->headers->get('Cache-Control'));
    $this->assertStringContainsString('public', $first->headers->get('Cache-Control'));

    $etag = $first->headers->get('ETag');
    $this->assertNotEmpty($etag);

    $this->getJson('/api/v1/about?lang=en', ['If-None-Match' => $etag])
      ->assertStatus(304);
  }

  /**
   * The ETag is derived from the rendered body, so each language has its own.
   */
  public function test_about_etag_differs_per_language(): void
  {
    $english = $this->getJson('/api/v1/about?lang=en')->headers->get('ETag');
    $arabic = $this->getJson('/api/v1/about?lang=ar')->headers->get('ETag');

    $this->assertNotSame($english, $arabic);
  }
}
