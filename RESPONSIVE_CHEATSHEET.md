# 📱 Responsive Cheat Sheet - Quick Reference

## 🎯 Breakpoints
```
Mobile:   < 640px   (default)
Tablet:   ≥ 640px   (sm:)
Desktop:  ≥ 1024px  (lg:)
```

---

## 🔧 Common Patterns

### 1. Responsive Padding
```html
<!-- Mobile: 16px, Desktop: 24px -->
<div class="p-4 sm:p-6">

<!-- Mobile: 12px, Desktop: 24px (horizontal) -->
<div class="px-3 sm:px-6">
```

### 2. Responsive Text
```html
<!-- Headings -->
<h1 class="text-lg sm:text-xl">            <!-- 18px → 20px -->
<h2 class="text-base sm:text-lg">          <!-- 16px → 18px -->

<!-- Numbers/Stats -->
<span class="text-2xl sm:text-3xl">        <!-- 24px → 30px -->

<!-- Body Text -->
<p class="text-xs sm:text-sm">             <!-- 12px → 14px -->
```

### 3. Responsive Grid
```html
<!-- Mobile: 1 col, Desktop: 2 cols -->
<div class="grid gap-4 lg:grid-cols-2">

<!-- Mobile: 1 col, Tablet: 3 cols -->
<div class="grid gap-4 md:grid-cols-3">
```

### 4. Responsive Buttons
```html
<!-- Full width mobile, auto width desktop -->
<button class="w-full sm:w-auto px-3 py-2 sm:px-4 sm:py-2 text-xs sm:text-sm">

<!-- Compact mobile, normal desktop -->
<a class="inline-flex gap-2 px-3 sm:px-5 py-2 text-xs sm:text-sm">
  <svg class="h-4 w-4" />
  <span class="hidden sm:inline">Long Label</span>
  <span class="sm:hidden">Short</span>
</a>
```

### 5. Responsive Forms
```html
<!-- Stacked mobile, inline desktop -->
<form class="flex flex-col sm:flex-row gap-3">
  <input class="w-full sm:w-64" />
  <button class="w-full sm:w-auto" />
</form>
```

### 6. Show/Hide Elements
```html
<!-- Hide on mobile, show on desktop -->
<div class="hidden lg:block">Desktop Only</div>

<!-- Show on mobile, hide on desktop -->
<div class="lg:hidden">Mobile Only</div>
```

### 7. Table → Cards
```html
<!-- Desktop: Table -->
<div class="hidden lg:block">
  <table>...</table>
</div>

<!-- Mobile: Cards -->
<div class="lg:hidden space-y-3">
  <div class="rounded-2xl border p-4">
    <!-- Card content -->
  </div>
</div>
```

---

## 🎨 Component Recipes

### Card Component
```html
<div class="rounded-2xl sm:rounded-3xl border border-slate-800 bg-slate-900/70 p-4 sm:p-6">
  <h2 class="text-base sm:text-lg font-semibold">Title</h2>
  <p class="mt-2 text-xs sm:text-sm text-slate-400">Description</p>
</div>
```

### Button Group
```html
<div class="flex flex-wrap items-center gap-2 sm:gap-3">
  <button class="px-3 sm:px-4 py-2 text-xs sm:text-sm">Action 1</button>
  <button class="px-3 sm:px-4 py-2 text-xs sm:text-sm">Action 2</button>
</div>
```

### Badge
```html
<span class="inline-flex items-center rounded-full border px-2 sm:px-3 py-0.5 sm:py-1 text-xs font-semibold">
  Badge
</span>
```

### Header Section
```html
<div class="flex flex-col gap-3 sm:gap-4 px-4 sm:px-6 py-4 sm:py-5 md:flex-row md:items-center md:justify-between">
  <div>
    <h1 class="text-lg sm:text-xl font-semibold">Page Title</h1>
    <p class="text-xs sm:text-sm text-slate-400">Description</p>
  </div>
  <div class="flex flex-wrap gap-2 sm:gap-3">
    <!-- Actions -->
  </div>
</div>
```

---

## ✅ DO's

```html
✅ Use responsive utilities
<div class="p-4 sm:p-6">

✅ Mobile-first approach
<div class="text-sm lg:text-base">

✅ Flexible widths
<input class="w-full sm:w-64">

✅ Wrap buttons
<div class="flex flex-wrap gap-2">

✅ Hide/show strategically
<div class="hidden lg:block">

✅ Proper spacing scale
gap-2 sm:gap-3 lg:gap-4
```

---

## ❌ DON'Ts

```html
❌ Fixed widths without breakpoints
<div class="w-96">

❌ Same padding everywhere
<div class="p-6">

❌ Desktop-only sizes
<div class="text-xl">

❌ No wrapping for groups
<div class="flex gap-3">

❌ Horizontal overflow
<table class="w-full">  <!-- Add overflow-x-auto parent -->

❌ Custom CSS when Tailwind exists
style="padding: 24px"
```

---

## 🚦 Testing Quick Check

### Mobile (375px)
```
□ Hamburger menu visible & works
□ No horizontal scroll
□ Text readable (≥ 12px)
□ Buttons touchable (≥ 44px)
□ Forms full width
□ Cards instead of tables
```

### Desktop (1280px)
```
□ Sidebar static visible
□ Tables instead of cards
□ Proper grid layouts
□ Hover effects work
□ All labels visible
```

---

## 📏 Size Reference

### Spacing Scale
```
gap-2  = 8px
gap-3  = 12px
gap-4  = 16px
gap-6  = 24px

p-3    = 12px
p-4    = 16px
p-6    = 24px
```

### Font Sizes
```
text-xs   = 12px
text-sm   = 14px
text-base = 16px
text-lg   = 18px
text-xl   = 20px
text-2xl  = 24px
text-3xl  = 30px
```

### Min Touch Target
```
Buttons: min 44x44px
Links: min 44x44px
```

---

## 🔥 Pro Tips

1. **Always test at 375px** (iPhone SE - smallest common phone)
2. **Use DevTools Device Mode** (Ctrl+Shift+M)
3. **Test real device** before deploy
4. **Check both portrait & landscape**
5. **Verify touch targets** (tap with finger, not mouse)
6. **Watch for text overflow** (use truncate if needed)
7. **Test forms** (can you fill & submit easily?)

---

## 🎯 Golden Rules

1. **Mobile First** - Design for mobile, enhance for desktop
2. **Progressive Enhancement** - Add features as screen grows
3. **Touch Friendly** - 44px minimum for interactive elements
4. **Readable Text** - 14px+ for body, 12px+ minimum
5. **No Horizontal Scroll** - Width must fit viewport
6. **Flexible Layouts** - Use flex/grid with wrapping
7. **Test Real Devices** - Emulators ≠ Real experience

---

## 📱 Device Presets

Quick test these widths:
```
375px  - iPhone SE
390px  - iPhone 12
768px  - iPad
1024px - Desktop
```

---

## ⌨️ DevTools Shortcuts

```
Ctrl+Shift+M  - Toggle device toolbar
Ctrl+Shift+C  - Inspect element
Ctrl+R        - Reload
Ctrl+Shift+R  - Hard reload
```

---

**Remember**: If it looks good on iPhone SE (375px) and Desktop (1280px), it'll look good everywhere! 📱💻