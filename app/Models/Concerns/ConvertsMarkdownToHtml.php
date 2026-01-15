<?php

namespace App\Models\Concerns;

trait ConvertsMarkdownToHtml
{
    /**
     * Trait 的 boot 方法。
     *
     * Eloquent 會在模型啟動時呼叫 trait 的 `boot[TraitName]` 靜態方法。
     * 這裡透過 `static::saving` 在模型儲存前將 Markdown 欄位轉為 HTML。
     *
     * 使用 `str($text)->markdown()` 解析 Markdown。
     *
     * 已重構說明：
     * - 改為使用 `getMarkdownToHtmlMap()` 來定義來源欄位到目標 HTML 欄位的映射，
     *   以支援多欄位轉換並避免重複程式碼。
     * - 透過 `collect(...)->flip()->map(...)` 將來源欄位逐一以
     *   `str($value)->markdown(self::getMarkdownSecurityOptions())` 轉換，
     *   最後以一次性 `fill()` 填入模型，確保行為一致且易於延展。
     */
    protected static function bootConvertsMarkdownToHtml()
    {
        static::saving(
            function (self $model) {

                $markdownData = collect(self::getMarkdownToHtmlMap())
                    ->flip()
                    ->map(fn($bodyColumn) => str($model->$bodyColumn)->markdown(
                        self::getMarkdownSecurityOptions()
                    ));

                $model->fill($markdownData->all());
            }
        );
    }

    protected static function getMarkdownToHtmlMap(): array
    {
        return [
            'body' => 'html',
        ];
    }

    /**
     * Markdown 安全選項：
     * - 'html_input' => 'strip'            移除原生 HTML（降低 XSS 風險）
     * - 'allow_unsafe_links' => false     禁止不安全連結（例如 javascript:）
     * - 'max_nesting_level' => 5          限制巢狀深度以降低資源濫用風險
     * - 'max_delimiters_per_line' => 10   限制每行分隔符數量
     * 如需調整，請評估安全性與效能。
     */
    protected static function getMarkdownSecurityOptions(): array
    {
        return [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 5,
            'max_delimiters_per_line' => 10
        ];
    }
}
