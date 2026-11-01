<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
  /** @use HasFactory<\Database\Factories\PageSectionFactory> */
  use HasFactory;

  /**
   * Supported block kinds. The API turns each kind into its own JSON array.
   */
  public const TYPES = ['text', 'stats', 'contact', 'social'];

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'page_id',
    'type',
    'heading_en',
    'heading_ar',
    'body_en',
    'body_ar',
    'value',
    'sort_order',
  ];

  /**
   * The page this block belongs to.
   */
  public function page(): BelongsTo
  {
    return $this->belongsTo(Page::class);
  }
}
