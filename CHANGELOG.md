## Update — 2026-05-13 16:21:55 UTC (branch: develop)

### ✨ Features
- feat(pages): Display titles and contents in interface language with defaultLanguage fallback (`ec12788`) by @ZHAO
- feat(news): display news list in interface language with fallback (`47853e6`) by @ZHAO
- feature(resources):share resources across pages and news (`ff551c9`) by @ZHAO
- feat(resources): add resource button to pages/news and fix clipboard copy (`5f5f96c`) by @ZHAO
- feat(pages):make the page name no-editable (`86144f6`) by @ZHAO
- feat(page title):Prevent editing of the page title (`9eadf68`) by @ZHAO
- feat(news): improve controller validation and i18n (`0751c4e`) by @ZHAO
- feat(news): improve form UX and add content validation (`5ea8596`) by @ZHAO
- feat(news):Limit the news content to 5,000 characters. (`f9663c9`) by @ZHAO
- feat(style):polish layout (`14ed44e`) by @ZHAO
- feat(news): paginate list and improve editor/content constraints (`ce4b99b`) by @ZHAO
- feat(news): add pagination to journal news list (`e9d124b`) by @ZHAO
- feat(news):Inject CKEditor into the news page for display and creation. (`a933f87`) by @ZHAO
- feat(news): add delete functionality with confirmation modal (`59dee85`) by @ZHAO
- feat(news): enable editing news using existing create form (`8aab8d7`) by @ZHAO
- feat(edit):edit form for news (`4169ac2`) by @ZHAO
- feat(news):install package twig/intl-extra (`fdf7be5`) by @ZHAO
- feat(news):create form to add and edit news (`29f1cfc`) by @ZHAO
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(pages):Remove unused homeLink variable (`a07a36e`) by @ZHAO
- fix(breadcrumb): add missing parent-title attributes to mobile menu (`195b606`) by @ZHAO
- fix(pages): complete non-editable page title implementation (`313af37`) by @ZHAO
- fix(security): add escapeHtml function to prevent XSS in journalPages (`2a09c32`) by @ZHAO
- fix(style):Improve the styling of the language widget (`2a7eb65`) by @ZHAO
- fix(csrf): remove journal-settings from stateless token IDs (`c3362dc`) by @ZHAO
- fix the yarn version and share (`9820e80`) by @ZHAO
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: standardize CSS units and colors with SCSS variables (`3dd3062`) by @ZHAO
- style: fix Prettier formatting in news.js (`076634f`) by @ZHAO
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(journalPages): centralize translations and remove redundant code (`ccf6215`) by @ZHAO
- refactor(ui):unify all notifications to bottom-right position (`d071de9`) by @ZHAO
- refactor:add component for flash message (`6b5acdb`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`6f80198`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`69e3b57`) by @ZHAO
- refactor(language-widget): make component reusable with dynamic widgetId (`dc24246`) by @ZHAO
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-05-13 13:06:53 UTC (branch: develop)

### ✨ Features
- feat(news): display news list in interface language with fallback (`47853e6`) by @ZHAO
- feature(resources):share resources across pages and news (`ff551c9`) by @ZHAO
- feat(resources): add resource button to pages/news and fix clipboard copy (`5f5f96c`) by @ZHAO
- feat(pages):make the page name no-editable (`86144f6`) by @ZHAO
- feat(page title):Prevent editing of the page title (`9eadf68`) by @ZHAO
- feat(news): improve controller validation and i18n (`0751c4e`) by @ZHAO
- feat(news): improve form UX and add content validation (`5ea8596`) by @ZHAO
- feat(news):Limit the news content to 5,000 characters. (`f9663c9`) by @ZHAO
- feat(style):polish layout (`14ed44e`) by @ZHAO
- feat(news): paginate list and improve editor/content constraints (`ce4b99b`) by @ZHAO
- feat(news): add pagination to journal news list (`e9d124b`) by @ZHAO
- feat(news):Inject CKEditor into the news page for display and creation. (`a933f87`) by @ZHAO
- feat(news): add delete functionality with confirmation modal (`59dee85`) by @ZHAO
- feat(news): enable editing news using existing create form (`8aab8d7`) by @ZHAO
- feat(edit):edit form for news (`4169ac2`) by @ZHAO
- feat(news):install package twig/intl-extra (`fdf7be5`) by @ZHAO
- feat(news):create form to add and edit news (`29f1cfc`) by @ZHAO
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(pages):Remove unused homeLink variable (`a07a36e`) by @ZHAO
- fix(breadcrumb): add missing parent-title attributes to mobile menu (`195b606`) by @ZHAO
- fix(pages): complete non-editable page title implementation (`313af37`) by @ZHAO
- fix(security): add escapeHtml function to prevent XSS in journalPages (`2a09c32`) by @ZHAO
- fix(style):Improve the styling of the language widget (`2a7eb65`) by @ZHAO
- fix(csrf): remove journal-settings from stateless token IDs (`c3362dc`) by @ZHAO
- fix the yarn version and share (`9820e80`) by @ZHAO
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: standardize CSS units and colors with SCSS variables (`3dd3062`) by @ZHAO
- style: fix Prettier formatting in news.js (`076634f`) by @ZHAO
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(journalPages): centralize translations and remove redundant code (`ccf6215`) by @ZHAO
- refactor(ui):unify all notifications to bottom-right position (`d071de9`) by @ZHAO
- refactor:add component for flash message (`6b5acdb`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`6f80198`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`69e3b57`) by @ZHAO
- refactor(language-widget): make component reusable with dynamic widgetId (`dc24246`) by @ZHAO
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-05-06 12:18:45 UTC (branch: develop)

### ✨ Features
- feat(pages):make the page name no-editable (`86144f6`) by @ZHAO
- feat(page title):Prevent editing of the page title (`9eadf68`) by @ZHAO
- feat(news): improve controller validation and i18n (`0751c4e`) by @ZHAO
- feat(news): improve form UX and add content validation (`5ea8596`) by @ZHAO
- feat(news):Limit the news content to 5,000 characters. (`f9663c9`) by @ZHAO
- feat(style):polish layout (`14ed44e`) by @ZHAO
- feat(news): paginate list and improve editor/content constraints (`ce4b99b`) by @ZHAO
- feat(news): add pagination to journal news list (`e9d124b`) by @ZHAO
- feat(news):Inject CKEditor into the news page for display and creation. (`a933f87`) by @ZHAO
- feat(news): add delete functionality with confirmation modal (`59dee85`) by @ZHAO
- feat(news): enable editing news using existing create form (`8aab8d7`) by @ZHAO
- feat(edit):edit form for news (`4169ac2`) by @ZHAO
- feat(news):install package twig/intl-extra (`fdf7be5`) by @ZHAO
- feat(news):create form to add and edit news (`29f1cfc`) by @ZHAO
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(breadcrumb): add missing parent-title attributes to mobile menu (`195b606`) by @ZHAO
- fix(pages): complete non-editable page title implementation (`313af37`) by @ZHAO
- fix(security): add escapeHtml function to prevent XSS in journalPages (`2a09c32`) by @ZHAO
- fix(style):Improve the styling of the language widget (`2a7eb65`) by @ZHAO
- fix(csrf): remove journal-settings from stateless token IDs (`c3362dc`) by @ZHAO
- fix the yarn version and share (`9820e80`) by @ZHAO
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting in news.js (`076634f`) by @ZHAO
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(ui):unify all notifications to bottom-right position (`d071de9`) by @ZHAO
- refactor:add component for flash message (`6b5acdb`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`6f80198`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`69e3b57`) by @ZHAO
- refactor(language-widget): make component reusable with dynamic widgetId (`dc24246`) by @ZHAO
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-05-05 19:47:20 UTC (branch: develop)

### ✨ Features
- feat(pages):make the page name no-editable (`86144f6`) by @ZHAO
- feat(page title):Prevent editing of the page title (`9eadf68`) by @ZHAO
- feat(news): improve controller validation and i18n (`0751c4e`) by @ZHAO
- feat(news): improve form UX and add content validation (`5ea8596`) by @ZHAO
- feat(news):Limit the news content to 5,000 characters. (`f9663c9`) by @ZHAO
- feat(style):polish layout (`14ed44e`) by @ZHAO
- feat(news): paginate list and improve editor/content constraints (`ce4b99b`) by @ZHAO
- feat(news): add pagination to journal news list (`e9d124b`) by @ZHAO
- feat(news):Inject CKEditor into the news page for display and creation. (`a933f87`) by @ZHAO
- feat(news): add delete functionality with confirmation modal (`59dee85`) by @ZHAO
- feat(news): enable editing news using existing create form (`8aab8d7`) by @ZHAO
- feat(edit):edit form for news (`4169ac2`) by @ZHAO
- feat(news):install package twig/intl-extra (`fdf7be5`) by @ZHAO
- feat(news):create form to add and edit news (`29f1cfc`) by @ZHAO
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(breadcrumb): add missing parent-title attributes to mobile menu (`195b606`) by @ZHAO
- fix(pages): complete non-editable page title implementation (`313af37`) by @ZHAO
- fix(security): add escapeHtml function to prevent XSS in journalPages (`2a09c32`) by @ZHAO
- fix(style):Improve the styling of the language widget (`2a7eb65`) by @ZHAO
- fix(csrf): remove journal-settings from stateless token IDs (`c3362dc`) by @ZHAO
- fix the yarn version and share (`9820e80`) by @ZHAO
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting in news.js (`076634f`) by @ZHAO
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(ui):unify all notifications to bottom-right position (`d071de9`) by @ZHAO
- refactor:add component for flash message (`6b5acdb`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`6f80198`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`69e3b57`) by @ZHAO
- refactor(language-widget): make component reusable with dynamic widgetId (`dc24246`) by @ZHAO
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-05-05 19:38:13 UTC (branch: develop)

### ✨ Features
- feat(pages):make the page name no-editable (`86144f6`) by @ZHAO
- feat(page title):Prevent editing of the page title (`9eadf68`) by @ZHAO
- feat(news): improve controller validation and i18n (`0751c4e`) by @ZHAO
- feat(news): improve form UX and add content validation (`5ea8596`) by @ZHAO
- feat(news):Limit the news content to 5,000 characters. (`f9663c9`) by @ZHAO
- feat(style):polish layout (`14ed44e`) by @ZHAO
- feat(news): paginate list and improve editor/content constraints (`ce4b99b`) by @ZHAO
- feat(news): add pagination to journal news list (`e9d124b`) by @ZHAO
- feat(news):Inject CKEditor into the news page for display and creation. (`a933f87`) by @ZHAO
- feat(news): add delete functionality with confirmation modal (`59dee85`) by @ZHAO
- feat(news): enable editing news using existing create form (`8aab8d7`) by @ZHAO
- feat(edit):edit form for news (`4169ac2`) by @ZHAO
- feat(news):install package twig/intl-extra (`fdf7be5`) by @ZHAO
- feat(news):create form to add and edit news (`29f1cfc`) by @ZHAO
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(breadcrumb): add missing parent-title attributes to mobile menu (`195b606`) by @ZHAO
- fix(pages): complete non-editable page title implementation (`313af37`) by @ZHAO
- fix(security): add escapeHtml function to prevent XSS in journalPages (`2a09c32`) by @ZHAO
- fix(style):Improve the styling of the language widget (`2a7eb65`) by @ZHAO
- fix(csrf): remove journal-settings from stateless token IDs (`c3362dc`) by @ZHAO
- fix the yarn version and share (`9820e80`) by @ZHAO
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(ui):unify all notifications to bottom-right position (`d071de9`) by @ZHAO
- refactor:add component for flash message (`6b5acdb`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`6f80198`) by @ZHAO
- refactor(pages):Refactor the code for the journal pages (`69e3b57`) by @ZHAO
- refactor(language-widget): make component reusable with dynamic widgetId (`dc24246`) by @ZHAO
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-29 19:34:43 UTC (branch: develop)

### ✨ Features
- feat(news): improve controller validation and i18n (`0751c4e`) by @ZHAO
- feat(news): improve form UX and add content validation (`5ea8596`) by @ZHAO
- feat(news):Limit the news content to 5,000 characters. (`f9663c9`) by @ZHAO
- feat(style):polish layout (`14ed44e`) by @ZHAO
- feat(news): paginate list and improve editor/content constraints (`ce4b99b`) by @ZHAO
- feat(news): add pagination to journal news list (`e9d124b`) by @ZHAO
- feat(news):Inject CKEditor into the news page for display and creation. (`a933f87`) by @ZHAO
- feat(news): add delete functionality with confirmation modal (`59dee85`) by @ZHAO
- feat(news): enable editing news using existing create form (`8aab8d7`) by @ZHAO
- feat(edit):edit form for news (`4169ac2`) by @ZHAO
- feat(news):install package twig/intl-extra (`fdf7be5`) by @ZHAO
- feat(news):create form to add and edit news (`29f1cfc`) by @ZHAO
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(security): add escapeHtml function to prevent XSS in journalPages (`2a09c32`) by @ZHAO
- fix(style):Improve the styling of the language widget (`2a7eb65`) by @ZHAO
- fix(csrf): remove journal-settings from stateless token IDs (`c3362dc`) by @ZHAO
- fix the yarn version and share (`9820e80`) by @ZHAO
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(language-widget): make component reusable with dynamic widgetId (`dc24246`) by @ZHAO
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-29 10:01:26 UTC (branch: develop)

### ✨ Features
- feat(news): improve controller validation and i18n (`0751c4e`) by @ZHAO
- feat(news): improve form UX and add content validation (`5ea8596`) by @ZHAO
- feat(news):Limit the news content to 5,000 characters. (`f9663c9`) by @ZHAO
- feat(style):polish layout (`14ed44e`) by @ZHAO
- feat(news): paginate list and improve editor/content constraints (`ce4b99b`) by @ZHAO
- feat(news): add pagination to journal news list (`e9d124b`) by @ZHAO
- feat(news):Inject CKEditor into the news page for display and creation. (`a933f87`) by @ZHAO
- feat(news): add delete functionality with confirmation modal (`59dee85`) by @ZHAO
- feat(news): enable editing news using existing create form (`8aab8d7`) by @ZHAO
- feat(edit):edit form for news (`4169ac2`) by @ZHAO
- feat(news):install package twig/intl-extra (`fdf7be5`) by @ZHAO
- feat(news):create form to add and edit news (`29f1cfc`) by @ZHAO
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(style):Improve the styling of the language widget (`2a7eb65`) by @ZHAO
- fix(csrf): remove journal-settings from stateless token IDs (`c3362dc`) by @ZHAO
- fix the yarn version and share (`9820e80`) by @ZHAO
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(language-widget): make component reusable with dynamic widgetId (`dc24246`) by @ZHAO
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-24 09:00:03 UTC (branch: develop)

### ✨ Features
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(csrf): remove journal-settings from stateless token IDs (`c3362dc`) by @ZHAO
- fix the yarn version and share (`9820e80`) by @ZHAO
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-23 14:01:52 UTC (branch: develop)

### ✨ Features
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix the yarn version and share (`9820e80`) by @ZHAO
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-20 08:49:52 UTC (branch: develop)

### ✨ Features
- feat(journalDetails): add back button to return to journal list (`1564b00`) by @ZHAO
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-19 20:09:06 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-17 19:45:18 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- chore: opt into Node.js 24 for CI and remove unused JS functions (`74fec53`) by @ZHAO
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-17 19:37:24 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- chore: update GitHub Actions to Node.js 24 and remove dead code (`5111e6a`) by @ZHAO
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-17 15:30:35 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix: resolve webpack-cli / webpack-encore dependency conflict (`e7f99ed`) by @ZHAO
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-17 15:25:17 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-17 15:19:20 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(pages): add HTML routes for page view and edit (`49c5ed8`) by @ZHAO
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-13 09:04:59 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-13 08:05:04 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix format (`de70bfc`) by @ZHAO
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-04-13 07:51:59 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(GFM): use semantic styling for page-body readonly display (`6dee64b`) by @ZHAO
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- refactor(journal): dynamic language support for content editing (`102bd11`) by @ZHAO
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-03-24 10:44:44 UTC (branch: develop)

### ✨ Features
- feat(journal): filtrer les revues par les rôles utilisateur (`acc2eb5`) by @ZHAO
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(security): allow public access to homepage without CAS authentication (`25fec9d`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-03-23 12:20:54 UTC (branch: develop)

### ✨ Features
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(changelog): use bash script to parse conventional commits (`74e55e6`) by @ZHAO
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO

---

## Update — 2026-03-23 10:30:30 UTC (branch: develop)

### ✨ Features
- feat: add Proposing Special Issues option and organize menu by categories (`e736479`) by @ZHAO
- feat:add a new page proposing special issues (`5619388`) by @ZHAO
- feat:restrict journal settings edit to epiadmin only (`ef95805`) by @ZHAO
- feat: prefill editor with existing content on translation edit (`2886a03`) by @ZHAO
- feat: language switch reload, page view fields, and sidebar language save (`33b6e2a`) by @ZHAO
- feat:add page title/content view fields (`2b38c55`) by @ZHAO
- feat: add translations widget to journal details sidebar (`443abb5`) by @ZHAO
- feat: add CSRF protection and restructure journal settings (`5c2a96a`) by @ZHAO
- feat: add environment-based logo loading with preprod/prod support (`d5caef5`) by @ZHAO
- feat: improve journal details page layout and responsiveness (`aa0decc`) by @ZHAO
- feat: add CSRF protection to journal settings (`22f889d`) by @ZHAO
- feat: display all pages defined in pages_hierarchy.yaml (`6465cf8`) by @ZHAO
- feat: improve journal configuration page (`d509d51`) by @ZHAO
- feat: add JSON Schema validation for journal configuration (`6eb3f67`) by @ZHAO
- feat(journal-config): add journal selection page with search and pagination (`5c751b2`) by @ZHAO
- feat(journal): add journal configuration management (`ac2ed34`) by @ZHAO
- feat(db): add JOURNAL_CONFIGURATION table (`b061631`) by @ZHAO
- feat: Preserve YAML order in page menu hierarchy (`d0784f6`) by @ZHAO
- feat(#95): Add support for 3-level breadcrumb hierarchy (`296db54`) by @ZHAO
- feat(#95):formatter le fichier scss (`5c8530b`) by @ZHAO
- feat(#95): Standardize CSS units to rem (`1b09e00`) by @ZHAO
- feat(#95): Add support for nested containers in page hierarchy (`86dcdfc`) by @ZHAO
- feat(#95):Add new pages for reviewers and conference organisers in Publish section (`c7aaa04`) by @ZHAO
- feat(#173);Add Introduction Board, Reviewers Board, and Operating Charter Board pages to the boards section navigation (`081c158`) by @ZHAO
- feat(#147):Change 'acknowledgements' to 'acknowledgments' to maintain consistency. (`87fd1f8`) by @ZHAO
- feat(#147): add Twig extension for page URL slug conversion (`e340033`) by @ZHAO
- feat(#147):Modify the indexing path (`6e1efe3`) by @ZHAO
- feat(#147):add new pages (acknowledgements) and  add URL alias system (acknowledgments → journal-acknowledgments) (`67d3bd9`) by @ZHAO
- feat(translations): add dynamic translation support for preview page button (`52dd644`) by @ZHAO
- feat: add preview page button for live site viewing (`4445f88`) by @ZHAO
- feat: add dynamic search clear functionality to journal page (`ded0d84`) by @ZHAO
- feat: implement resource usage validation and auto-generation features (`6c2fcde`) by @ZHAO
- feat: add resource usage check before deletion (`4dcaf32`) by @ZHAO
- feat: implement hierarchical page navigation for journal pages (`7158b4e`) by @ZHAO
- feat: enhance changelog workflow with proper configuration and multi-branch support (`d8ba10b`) by @ZHAO
- feat: add file conflict resolution modal with custom rename option (`2283148`) by @ZHAO
- feat: remove full URL option and improve resources interface (`e501527`) by @ZHAO
- feat:resolve linting errors (`04ca262`) by @ZHAO
- feat(editor): implement image insertion by URL in CKEditor and fix linting issue (`ee272d1`) by @ZHAO
- feat(resources): add journal context service and improve file management (`0820931`) by @ZHAO
- feat(resources): add file upload and file listing (`46a594b`) by @ZHAO

### 🐛 Bug Fixes
- fix(changelog): use bash script to parse conventional commits (`d211880`) by @ZHAO
- fix: resolve ESLint errors in journalSettings and journalDetails (`9213dec`) by @ZHAO
- fix: simplify logo loading with preprod fallback (`bb465bd`) by @ZHAO
- fix: add horizontal padding to journal layout on mobile and tablet (`baba849`) by @ZHAO
- fix: resolve PHPStan level 6 errors and add Doctrine extension (`ad4cd9b`) by @ZHAO
- fix: add PHPDoc types and resolve PHPStan errors (`5a592ce`) by @ZHAO
- fix: correct the translation of “volume” (`38f44a5`) by @ZHAO
- fix: language widget display and settings persistence (`70e889f`) by @ZHAO
- fix: add missing translations for authors and ethical charter menu options (`e216d81`) by @ZHAO
- fix: CSRF, XSS, access control and error disclosure fixes (`a62a350`) by @ZHAO
- fix:restore page content when cancelling inline edit (`60c6a3f`) by @ZHAO
- fix: language switch now works without manual page refresh (`5d7d643`) by @ZHAO
- fix: store user uid, lowercase page_code, and correct visibility format (`9572fde`) by @ZHAO
- fix: improve save error handling and button translation (`e07025a`) by @ZHAO
- fix(docker): add conditional cache warmup on container startup (`9868894`) by @ZHAO
- fix(ckeditor): add image configuration and resolve syntax errors (`b92191d`) by @ZHAO
- fix(preview): redirect container children to parent URLs (`2afe786`) by @ZHAO
- fix: javascript format (`3a18086`) by @ZHAO
- fix: add missing English translations for resource table headers (`a363b3f`) by @ZHAO
- fix:Le changelog ne change pas automatiquement. (`e32ec46`) by @ZHAO
- fix: simplify changelog workflow commit logic to prevent failures (`617e6c0`) by @ZHAO
- fix: correct changelog workflow template syntax (`e417950`) by @ZHAO
- fix: correct changelog workflow to prevent commit failures (`9baa82e`) by @ZHAO
- fix: correct changelog workflow template syntax (`da0464d`) by @ZHAO
- fix: add file existence validation for custom filename conflicts (`9a69ce7`) by @ZHAO
- fix the custom filename conflict issue (`ca7d505`) by @ZHAO
- fix errors format lint (`a1e876c`) by @ZHAO
- fix: repair delete functionality on resources page (`cd20187`) by @ZHAO
- fix the lint errors (`62d7ccd`) by @ZHAO
- fix the format javascript (`9e2707a`) by @ZHAO
- fix format javascript (`7c8c2a2`) by @ZHAO
- fix the format of resources.js (`01b914b`) by @ZHAO
- fix: make format for journalDetails (`cf83a9e`) by @ZHAO
- fix: resolve multilingual save success messages in page editor (`73cd194`) by @ZHAO
- fix: resolve ESLint/Prettier conflicts and improve code formatting coverage (`fefff50`) by @ZHAO
- fix: resolve code formatting and linting issues across project (`4556a0f`) by @ZHAO
- fix: apply Prettier formatting to resolve code style issues (`81ccc73`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`5d583dc`) by @ZHAO
- fix: correct code formatting in _header.js for CI (`51e38ea`) by @ZHAO

### 📝 Documentation
- docs: mise à jour README avec configuration URL preprod (`e7f6236`) by @ZHAO

### 🎨 Style
- style: fix Prettier formatting (`bb2706e`) by @ZHAO
- style: format journalDetails.js with Prettier (`616beef`) by @ZHAO
- style: fix indentation formatting in header script (`8bcf319`) by @ZHAO

### 🧰 Maintenance
- chore: add PHPStan and Rector tooling with Makefile commands (`c219870`) by @ZHAO
- refactor: unify route parameter name from rvcode to code (`26faf26`) by @ZHAO
- refactor:move setting options from template to service (`4e3c730`) by @ZHAO

### ⚙️ CI/CD
- ci: Force CI rebuild after successful local validation (`163a028`) by @ZHAO
- ci: Trigger CI rerun after code quality checks (`4b79d33`) by @ZHAO

### 🧪 Tests
- test configuration (`2e41b0f`) by @ZHAO
- test configuration (`7e0dc8d`) by @ZHAO


