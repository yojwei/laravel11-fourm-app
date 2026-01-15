<?php

namespace App\Models\Concerns;

trait ConvertsMarkdownToHtml
{
    /**
     * Trait 的 boot 方法。
     *
     * 為什麼可以用：Eloquent 在模型啟動時會自動尋找並呼叫 trait 中命名為
     * `boot[TraitName]` 的靜態方法（例如 `bootConvertsMarkdownToHtml`），因此當模型
     * 使用此 trait 時，這個方法會在模型啟動階段被呼叫。此處使用 `static::saving`
     * 註冊一個事件，確保每次模型儲存前都會將 Markdown 欄位轉換為 HTML。
     *
     * 註：`str($text)->markdown()` 是 Laravel 提供的 `str()` helper，回傳
     * `Illuminate\Support\Stringable`，該物件在框架中有 `markdown()` 擴充可用，
     * 因此可以直接呼叫來解析 Markdown。
     */
    protected static function bootConvertsMarkdownToHtml()
    {
        static::saving(fn(self $model) => $model->fill(['html' => str($model->body)->markdown(
            [
                // Markdown 安全性設定：
                // - 'html_input' => 'strip'
                //     從使用者輸入移除原生 HTML，以防止 XSS（例如禁止使用者提供 <script>
                //     或內聯事件處理器）。
                // - 'allow_unsafe_links' => false
                //     禁止 javascript: 或 data: 等不安全的連結類型，避免透過惡意 URL
                //     觸發 XSS 或其他攻擊。
                // - 'max_nesting_level' => 5
                //     限制巢狀層級深度，避免極深巢狀輸入導致大量運算或記憶體使用（可緩解
                //     特定類型的 DoS 攻擊）。
                // - 'max_delimiters_per_line' => 10
                //     限制每行分隔符的數量，避免極長或重複分隔符導致渲染或處理問題。
                // 修改這些值前，請評估安全性與效能的權衡，並根據應用需求做測試。
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                'max_nesting_level' => 5,
                'max_delimiters_per_line' => 10
            ]
        )]));
    }
}
