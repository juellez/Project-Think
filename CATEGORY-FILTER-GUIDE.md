# Project Category Filter - Implementation Guide

## Overview

This implementation adds a real-time category/subject filtering system to your WordPress site with **progressive enhancement**:
- ✅ **JavaScript enabled**: Filters update instantly via AJAX without page reload
- ✅ **JavaScript disabled**: Falls back to standard WordPress URL navigation
- ✅ **Works with**: Categories (Subjects), Tags, Search results, Archives

---

## 📦 What Was Added

### 1. **Custom Block: Project Category Filter**
Location: `/plugins/project-think/blocks/category-filter/`

Files:
- `block.json` - Block configuration
- `render.php` - Server-side rendering (outputs filter UI with proper URLs)
- `index.js` - Block editor interface

### 2. **Frontend Assets**
Location: `/plugins/project-think/assets/`

Files:
- `css/category-filter.css` - Styling for the filter (buttons, dropdown, list styles)
- `js/category-filter.js` - Real-time AJAX filtering logic

### 3. **Unified Templates**
Location: `/themes/project-think/templates/`

Templates:
- `archive.html` - For category/tag archives
- `search.html` - For search results
- `home.html` - For main projects page/blog index

All templates include:
- The Category Filter block
- Query Loop block for displaying posts
- Consistent layout and styling

---

## 🚀 How to Use

### Step 1: Add the Filter to Your /projects Page

1. **Go to** WordPress Admin → Pages → Edit your `/projects` page
2. **Add the block**: Click `+` → Search for "Project Category Filter"
3. **Place it** above your Query Loop block
4. **Configure** in the sidebar (right panel):
   - **Display Style**: Choose buttons, dropdown, or list
   - **Show "All Projects" option**: Toggle on/off
   - **Taxonomy**: Choose Categories/Subjects or Tags

### Step 2: Configure Your Query Loop

Make sure your Query Loop block has:
- CSS class: `wp-block-query` (default)
- Post template inside with CSS class: `wp-block-post-template` (default)

The JavaScript automatically detects these and enables real-time filtering.

### Step 3: Test the Implementation

**With JavaScript enabled:**
1. Click a category/subject button
2. Posts should filter instantly without page reload
3. Notice the loading indicator
4. Active filter should be highlighted

**With JavaScript disabled** (to test):
1. Open browser dev tools → Console
2. Type: `document.querySelector('.project-category-filter__link').click()`
3. Or disable JavaScript in browser settings
4. Click a category → Page navigates to standard WordPress category archive

---

## 🎨 Display Styles

### Buttons (default)
```
[All Projects] [Math] [Science] [History]
```
- Best for: 5-10 categories
- Visually prominent
- Easy to tap on mobile

### Dropdown
```
[Select a subject ▼]
```
- Best for: 10+ categories
- Saves space
- Clean, minimal look

### List
```
• All Projects
• Math (5)
• Science (12)
• History (8)
```
- Best for: Any number of categories
- Shows count
- Traditional appearance

---

## 🎯 How It Works

### Progressive Enhancement Flow

```
User clicks category
    ↓
JavaScript available?
    ↓
  YES → Fetch filtered posts via REST API
        Update DOM without reload
        Show loading state
        Update browser history
    ↓
  NO  → Follow href to category archive URL
        WordPress handles filtering
```

### Technical Details

1. **Server-side rendering** (`render.php`):
   - Generates proper URLs for each category (e.g., `/category/math/`)
   - Adds `data-*` attributes for JavaScript
   - Works perfectly without JavaScript

2. **JavaScript enhancement** (`category-filter.js`):
   - Listens for click events on filter links
   - Prevents default navigation
   - Fetches posts from `/wp-json/wp/v2/posts?category=X`
   - Renders results into existing Query Loop
   - Updates active state

3. **CSS** (`category-filter.css`):
   - Styles all three display modes
   - Loading states and animations
   - Responsive design
   - Smooth transitions

---

## 🔧 Customization

### Change Colors

Edit `/plugins/project-think/assets/css/category-filter.css`:

```css
/* Line 63: Button hover color */
.project-category-filter--buttons .project-category-filter__link:hover {
    background: #YOUR-COLOR; /* Change from #1a4548 */
}

/* Line 73: Active button color */
.project-category-filter--buttons .project-category-filter__link.is-active {
    background: #YOUR-COLOR;
}
```

### Change Number of Posts Per Page

Edit your Query Loop block settings:
- In the block editor, select the Query Loop
- Sidebar → Settings → Items per Page
- Default: 12

Or modify the JavaScript:
```javascript
// Line 148 in category-filter.js
let apiUrl = '/wp-json/wp/v2/posts?_embed=true&per_page=12'; // Change 12
```

### Add Tag Filtering

1. Add another Category Filter block
2. In block settings → Taxonomy → Select "Tags"
3. Can have multiple filters on the same page

### Modify Grid Layout

Edit template files (e.g., `archive.html`):
```html
<!-- Line 39: Change columnCount -->
"layout":{"type":"grid","columnCount":3}
```

Change `3` to `2` or `4` for different grid layouts.

---

## 🐛 Troubleshooting

### Filter Not Working (JavaScript)

**Check:**
1. Browser console for errors
2. Query Loop block has correct classes
3. JavaScript file is loading: View source → Search for `category-filter.js`

**Fix:**
```bash
# Clear WordPress cache
wp cache flush

# Or in wp-admin:
# Settings → Permalinks → Save (flushes rewrite rules)
```

### Styles Not Applying

**Check:**
1. CSS file is loading: View source → Search for `category-filter.css`
2. Theme is activated
3. Plugin is activated

**Fix:**
```bash
# Re-save theme
# Or hard refresh: Ctrl+Shift+R (Windows) / Cmd+Shift+R (Mac)
```

### Categories Not Showing

**Check:**
1. You have categories/subjects created
2. Categories have posts assigned
3. Posts are published (not drafts)

**Fix:**
- Go to Posts → Subjects
- Create at least one subject
- Assign it to published posts

### REST API Issues

**Check:**
```bash
# Test REST API directly
curl https://yoursite.com/wp-json/wp/v2/posts?per_page=1
```

If error, check:
- Permalinks set (not "Plain")
- REST API not disabled by plugin/security
- .htaccess file exists and is writable

---

## 📱 Mobile Responsiveness

All display styles are mobile-optimized:

- **Buttons**: Stack vertically on small screens
- **Dropdown**: Full width on mobile
- **List**: Compact spacing

Breakpoints:
- `768px` - Tablets
- `480px` - Mobile phones

---

## ♿ Accessibility

Features:
- ✅ Keyboard navigation (Tab, Enter)
- ✅ ARIA labels for screen readers
- ✅ Focus states
- ✅ Color contrast (WCAG AA)
- ✅ Loading announcements

---

## 🔄 Browser Support

| Browser | Version | Status |
|---------|---------|--------|
| Chrome  | 90+     | ✅ Full |
| Firefox | 88+     | ✅ Full |
| Safari  | 14+     | ✅ Full |
| Edge    | 90+     | ✅ Full |
| IE 11   | -       | ⚠️ Fallback only |

---

## 📝 Template Usage

### Using Templates Site-Wide

The created templates automatically apply to:

- `home.html` → Blog index / Projects page
- `archive.html` → Category archives, Tag archives, Date archives
- `search.html` → Search results

### Override Individual Templates

To customize a specific category archive:

1. Go to: Appearance → Editor → Templates
2. Click "Add New Template"
3. Choose "Category Archive"
4. Select specific category
5. Customize and save

---

## 🎓 Example: Adding to /projects Page

If you're using the **Site Editor** instead of the classic editor:

1. **Go to**: Appearance → Editor
2. **Click**: Templates → Add New Template → Page
3. **Choose**: Your /projects page
4. **Add blocks**:
   ```
   Header (Template Part)

   Group (main content)
     ├─ Heading: "Project Gallery"
     ├─ Paragraph: "Browse our projects"
     ├─ Project Category Filter block ← ADD THIS
     └─ Query Loop block
          └─ Post Template
                ├─ Featured Image
                ├─ Title
                ├─ Excerpt
                └─ Date

   Footer (Template Part)
   ```
5. **Save**

---

## 🚢 Deployment Checklist

Before going live:

- [ ] Test filtering with JavaScript enabled
- [ ] Test filtering with JavaScript disabled
- [ ] Test on mobile devices
- [ ] Test keyboard navigation
- [ ] Check all category links work
- [ ] Verify page load performance
- [ ] Test with 50+ posts
- [ ] Clear all caches
- [ ] Test in different browsers

---

## 📚 Files Reference

### Plugin Files
```
plugins/project-think/
├── project-think.php                    # Main plugin file (updated)
├── blocks/
│   └── category-filter/
│       ├── block.json                   # Block definition
│       ├── render.php                   # Server-side rendering
│       └── index.js                     # Block editor
└── assets/
    ├── css/
    │   └── category-filter.css          # Styles
    └── js/
        └── category-filter.js           # Frontend logic
```

### Theme Files
```
themes/project-think/
└── templates/
    ├── home.html                        # Projects index
    ├── archive.html                     # Category/tag archives
    └── search.html                      # Search results
```

---

## 🆘 Need Help?

### Common Questions

**Q: Can I use this with custom post types?**
A: Yes! Modify the Query Loop query parameter `"postType":"post"` to your custom post type slug.

**Q: Can I filter by multiple categories?**
A: The current implementation filters by single category. For multiple, you'd need to modify the JavaScript to accept arrays.

**Q: Does this work with Gutenberg blocks?**
A: Yes! It's built specifically for the Block Editor and Query Loop blocks.

**Q: Will this slow down my site?**
A: No. Assets only load when the filter block is present, and AJAX requests are lightweight.

---

## 🎉 You're Done!

Your WordPress site now has a modern, accessible, progressively enhanced category filter system. Enjoy!

For questions or issues, check:
- WordPress error logs: `/wp-content/debug.log`
- Browser console (F12)
- Network tab to see AJAX requests

---

**Version:** 1.0.0
**Last Updated:** 2025-11-20
**Compatible With:** WordPress 5.9+, PHP 7.4+
