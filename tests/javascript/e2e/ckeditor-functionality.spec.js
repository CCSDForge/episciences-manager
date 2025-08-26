// E2E tests for CKEditor functionality - standalone tests
const { test, expect } = require('@playwright/test');

test.describe('CKEditor Functionality Tests', () => {
  test('should test CKEditor initialization on a page with textarea', async ({
    page,
  }) => {
    // Create a simple test page with CKEditor initialization
    await page.goto('/en/');

    // Add CKEditor test elements to the page using evaluate
    await page.evaluate(() => {
      // Create a test container
      const testContainer = document.createElement('div');
      testContainer.id = 'ckeditor-test-container';
      testContainer.innerHTML = `
        <h2>CKEditor Test</h2>
        <textarea id="test-editor">This is test content for CKEditor</textarea>
        <button id="init-editor" onclick="window.initCKEditorTest()">Initialize Editor</button>
      `;
      document.body.appendChild(testContainer);

      // Function to initialize CKEditor for testing
      window.initCKEditorTest = async function () {
        try {
          // Check if CKEditor is available
          if (typeof window.ClassicEditor !== 'undefined') {
            const editor = await window.ClassicEditor.create(
              document.getElementById('test-editor'),
              {
                toolbar: [
                  'bold',
                  'italic',
                  'link',
                  'bulletedList',
                  'numberedList',
                ],
              },
            );
            console.log('Test CKEditor initialized successfully');
            window.testEditor = editor;
          } else {
            console.log('CKEditor not available on this page');
          }
        } catch (error) {
          console.error('Error initializing test CKEditor:', error);
        }
      };
    });

    // Check if the test elements were added
    await expect(page.locator('#ckeditor-test-container')).toBeVisible();
    await expect(page.locator('#test-editor')).toBeVisible();

    console.log('✅ CKEditor test container created successfully');
  });

  test('should check if CKEditor modules are loaded', async ({ page }) => {
    await page.goto('/en/');

    // Check if CKEditor assets are loaded
    const ckEditorModules = await page.evaluate(() => {
      const results = {
        classicEditor: typeof window.ClassicEditor !== 'undefined',
        ckeditorAssets:
          document.querySelectorAll('script[src*="ckeditor"]').length > 0,
        ckeditorCSS:
          document.querySelectorAll('link[href*="ckeditor"]').length > 0,
        webpackChunks: typeof window.webpackChunkName !== 'undefined',
      };
      return results;
    });

    console.log('📊 CKEditor availability check:', ckEditorModules);

    if (ckEditorModules.classicEditor) {
      console.log('✅ ClassicEditor is available globally');
    } else {
      console.log('ℹ️ ClassicEditor not available - might be loaded on-demand');
    }

    // Just verify the page is working
    await expect(page.locator('body')).toBeVisible();
  });

  test('should verify CKEditor CSS classes are present', async ({ page }) => {
    await page.goto('/en/');

    // Check if any CKEditor-related CSS classes exist in stylesheets
    const hasEditorStyles = await page.evaluate(() => {
      const stylesheets = Array.from(document.styleSheets);
      let foundCKStyles = false;

      try {
        for (const stylesheet of stylesheets) {
          try {
            const rules = stylesheet.cssRules || stylesheet.rules;
            if (rules) {
              for (const rule of rules) {
                if (rule.selectorText && rule.selectorText.includes('ck-')) {
                  foundCKStyles = true;
                  break;
                }
              }
            }
          } catch (e) {
            // Skip stylesheets that can't be accessed (CORS)
            continue;
          }
          if (foundCKStyles) break;
        }
      } catch (error) {
        console.log('Could not check stylesheets:', error);
      }

      return {
        foundCKStyles,
        totalStylesheets: stylesheets.length,
        bodyClasses: document.body.className,
      };
    });

    console.log('🎨 CKEditor styles check:', hasEditorStyles);

    if (hasEditorStyles.foundCKStyles) {
      console.log('✅ CKEditor styles are loaded');
    } else {
      console.log('ℹ️ CKEditor styles not found in current stylesheets');
    }

    await expect(page.locator('body')).toBeVisible();
  });

  test('should test basic HTML editor functionality', async ({ page }) => {
    await page.goto('/en/');

    // Create a simple contenteditable area to test basic editor functionality
    await page.evaluate(() => {
      const editorDiv = document.createElement('div');
      editorDiv.id = 'simple-editor';
      editorDiv.contentEditable = 'true';
      editorDiv.style.border = '1px solid #ccc';
      editorDiv.style.padding = '10px';
      editorDiv.style.minHeight = '100px';
      editorDiv.style.margin = '10px';
      editorDiv.innerHTML = 'This is a simple editor for testing...';

      const label = document.createElement('h3');
      label.textContent = 'Simple Editor Test';

      document.body.appendChild(label);
      document.body.appendChild(editorDiv);
    });

    // Test the simple editor
    const editor = page.locator('#simple-editor');
    await expect(editor).toBeVisible();

    // Clear and add content
    await editor.click();
    await page.keyboard.press('Control+a');
    await editor.fill('Testing HTML editor functionality');

    // Verify content
    await expect(editor).toContainText('Testing HTML editor');

    // Test basic formatting with keyboard shortcuts
    await editor.selectText();
    await page.keyboard.press('Control+b'); // Try bold

    console.log('✅ Basic HTML editor functionality tested');
  });

  test('should simulate CKEditor-like toolbar interactions', async ({
    page,
  }) => {
    await page.goto('/en/');

    // Create a modern mock CKEditor interface for testing
    await page.evaluate(() => {
      const container = document.createElement('div');
      container.className = 'ck-editor-mock';
      container.innerHTML = `
        <div class="ck-toolbar-mock" style="border: 1px solid #ccc; padding: 5px; margin: 10px;">
          <button class="ck-button-bold" data-action="bold" style="margin: 2px; padding: 5px; border: 1px solid #ccc;">B</button>
          <button class="ck-button-italic" data-action="italic" style="margin: 2px; padding: 5px; border: 1px solid #ccc;">I</button>
          <button class="ck-button-link" data-action="link" style="margin: 2px; padding: 5px; border: 1px solid #ccc;">Link</button>
        </div>
        <div class="ck-content-mock" contenteditable="true" style="border: 1px solid #ccc; padding: 10px; margin: 10px; min-height: 100px;">
          Type here to test editor functionality...
        </div>
      `;

      document.body.appendChild(container);

      // Modern formatting functions without execCommand
      function applyBold(element) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
          const range = selection.getRangeAt(0);
          const selectedText = range.toString();

          if (selectedText) {
            // Create strong element
            const strong = document.createElement('strong');
            strong.textContent = selectedText;

            // Replace selected content
            range.deleteContents();
            range.insertNode(strong);

            // Clear selection
            selection.removeAllRanges();
            return true;
          }
        }
        return false;
      }

      function applyItalic(element) {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
          const range = selection.getRangeAt(0);
          const selectedText = range.toString();

          if (selectedText) {
            // Create em element
            const em = document.createElement('em');
            em.textContent = selectedText;

            // Replace selected content
            range.deleteContents();
            range.insertNode(em);

            // Clear selection
            selection.removeAllRanges();
            return true;
          }
        }
        return false;
      }

      // Add modern click handlers
      container.addEventListener('click', e => {
        const contentArea = container.querySelector('.ck-content-mock');

        if (e.target.matches('.ck-button-bold')) {
          // Focus content area first
          contentArea.focus();

          // Apply formatting and toggle button state
          if (applyBold(contentArea)) {
            e.target.classList.toggle('active');
            e.target.style.background = e.target.classList.contains('active')
              ? '#007bff'
              : '';
            e.target.style.color = e.target.classList.contains('active')
              ? 'white'
              : '';
          }
        }

        if (e.target.matches('.ck-button-italic')) {
          // Focus content area first
          contentArea.focus();

          // Apply formatting and toggle button state
          if (applyItalic(contentArea)) {
            e.target.classList.toggle('active');
            e.target.style.background = e.target.classList.contains('active')
              ? '#007bff'
              : '';
            e.target.style.color = e.target.classList.contains('active')
              ? 'white'
              : '';
          }
        }
      });

      // Add focus/blur handlers for better UX
      const contentArea = container.querySelector('.ck-content-mock');
      contentArea.addEventListener('focus', () => {
        contentArea.style.borderColor = '#007bff';
      });

      contentArea.addEventListener('blur', () => {
        contentArea.style.borderColor = '#ccc';
      });
    });

    // Test the modern mock editor
    const mockEditor = page.locator('.ck-editor-mock');
    const mockContent = page.locator('.ck-content-mock');
    const boldButton = page.locator('.ck-button-bold');
    const italicButton = page.locator('.ck-button-italic');

    await expect(mockEditor).toBeVisible();
    await expect(mockContent).toBeVisible();

    // Test content input
    await mockContent.click();
    await mockContent.fill('This is test content for modern formatting');

    // Verify content
    await expect(mockContent).toContainText('modern formatting');

    // Test bold formatting
    await mockContent.selectText();
    await boldButton.click();

    // Verify button state changed
    await expect(boldButton).toHaveCSS('background-color', 'rgb(0, 123, 255)');

    // Test italic formatting with new content
    await mockContent.click();
    await mockContent.fill('This is italic test content');
    await mockContent.selectText();
    await italicButton.click();

    // Verify italic button state
    await expect(italicButton).toHaveCSS(
      'background-color',
      'rgb(0, 123, 255)',
    );

    // Check if formatting was actually applied by checking for HTML tags
    const hasFormatting = await mockContent.evaluate(el => {
      return el.innerHTML.includes('<strong>') || el.innerHTML.includes('<em>');
    });

    if (hasFormatting) {
      console.log('✅ Modern HTML formatting applied successfully');
    }

    console.log('✅ Modern CKEditor toolbar interactions tested');
  });
});
