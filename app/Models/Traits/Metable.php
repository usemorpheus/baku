<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 * @property array $meta
 */
trait Metable
{
    protected string $metaField = 'meta';

    public function setMeta($key, $value = null): void
    {
        $meta = $this->meta;

        if (is_string($key)) {
            $key = [
                $key => $value,
            ];
        }
        foreach ($key as $_key => $_value) {
            $meta[$_key] = $_value;
        }

        $this->syncMeta($meta);
    }

    public function getMeta($key = null, $default = null)
    {
        $meta = $this->meta;

        if (empty($key)) {
            return $meta;
        }
        return $meta[$key] ?? $default;
    }

    public function clearMeta(): void
    {
        $this->syncMeta();
    }

    public function syncMeta($meta = []): void
    {
        $this->update([$this->getMetaField() => $meta]);
    }

    public function getMetaAttribute()
    {
        return to_json($this->attributes[$this->getMetaField()] ?: []);
    }

    protected function getMetaField(): string
    {
        return $this->metaField;
    }
}
