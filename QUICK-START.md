# Quick Start Guide - Category Filter

## In 3 Steps:

### 1️⃣ Add the Block
```
WordPress Admin → Edit /projects page
→ Click + → Search "Project Category Filter"
→ Place above Query Loop
```

### 2️⃣ Choose Display Style
```
Block Settings (sidebar):
- Display Style: Buttons (recommended)
- Show "All Projects": ✓ ON
- Taxonomy: Categories/Subjects
```

### 3️⃣ Test It
```
Preview page → Click a category
→ Posts filter instantly ✨
```

---

## Display Options

### 🔘 Buttons (Best for 5-10 categories)
Visually prominent, mobile-friendly
```
Settings → Display Style: Buttons
```

### 📋 Dropdown (Best for 10+ categories)
Compact, saves space
```
Settings → Display Style: Dropdown
```

### 📝 List (Traditional)
Shows counts, works for any number
```
Settings → Display Style: List
```

---

## Works Everywhere

✅ /projects page
✅ Category archives
✅ Search results
✅ Tag archives
✅ Custom archives

---

## Progressive Enhancement

| Scenario | Behavior |
|----------|----------|
| JS enabled | Instant AJAX filtering |
| JS disabled | Standard page navigation |
| REST API issue | Fallback to URLs |

No broken experience - ever! 🎉

---

## Customization

### Change Colors
Edit: `/plugins/project-think/assets/css/category-filter.css`
Look for: `#1a4548` (your theme primary color)

### Grid Layout
Edit template files (e.g., `archive.html`)
Change: `"columnCount":3` to `2` or `4`

### Posts Per Page
Edit Query Loop → Settings → Items per Page

---

## Troubleshooting

### Filter not working?
1. Check browser console for errors
2. Verify Query Loop block exists on page
3. Clear WordPress cache

### Styles not applying?
1. Hard refresh: Ctrl+Shift+R (Windows) / Cmd+Shift+R (Mac)
2. Check plugin is activated
3. Resave Permalinks: Settings → Permalinks → Save

### No categories showing?
1. Create categories: Posts → Subjects
2. Assign to published posts
3. Refresh page

---

## Need More Help?

📖 See `CATEGORY-FILTER-GUIDE.md` for full documentation

🔍 Check browser console (F12) for errors

🐛 Look at `/wp-content/debug.log` for WordPress errors

---

**That's it!** You now have real-time category filtering 🚀
