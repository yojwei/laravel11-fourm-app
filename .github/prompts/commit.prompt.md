---
agent: agent
---

我需要你完成下列步驟：

1. 檢視 `get_changed_files`
2. 分析變更內容，根據 Conventional Commits 規範進行分類
3. 為每個邏輯變更產生符合 #file:../../.copilot-commit-message-instructions.md 規範的 Commit Message
4. 執行 `git add` 並 commit（若有多個不同類型的變更，需分別 commit）

# 分類原則

根據 Conventional Commits 規範，請按照**變更的實際性質**進行分類，而非固定順序：

## 主要類型

1. **feat**: 新增功能或特性
   - 新增 API 端點、新增元件、新增業務邏輯等
   
2. **fix**: 修復 bug
   - 修正錯誤行為、修正安全問題等

3. **test**: 新增或修改測試
   - 單元測試、整合測試、E2E 測試等
   - 若測試是配合新功能，可考慮與 feat 一起 commit

4. **docs**: 文件變更
   - README、註解、說明文件等
   - 僅修改文件，不涉及程式碼邏輯

5. **chore**: 雜項變更
   - 依賴套件更新、設定檔調整、建置流程等
   - 不影響實際功能的維護性工作

6. **refactor**: 重構
   - 改善程式碼結構但不改變功能
   
7. **style**: 程式碼風格調整
   - 格式化、排版、命名調整等

8. **perf**: 效能優化
   - 改善執行效能的變更

## 分類策略

- **優先按功能分組**：相關的變更（如功能 + 測試 + 路由）可以一起 commit
- **獨立不相關的變更**：不同功能或類型的變更應分開 commit
- **測試變更**：
  - 若測試是新功能的一部分 → 與 `feat` 一起
  - 若僅補充既有功能的測試 → 獨立 `test` commit
- **套件管理**：
  - 新增依賴套件支援新功能 → 與 `feat` 一起
  - 單純更新依賴版本 → 獨立 `chore` commit

## Commit Message 格式

```
<type>(<scope>): <description>

[optional body]
```

- **type**: 必填，使用上述類型
- **scope**: 選填，變更的範疇（如 api, ui, auth 等）
- **description**: 必填，**使用繁體中文**，簡潔描述變更內容
