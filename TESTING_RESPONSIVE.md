# 📱 Testing Guide: Responsivitas UI

## Quick Test Checklist

### ✅ Mobile View (< 640px)
- [ ] Hamburger menu terlihat dan berfungsi
- [ ] Sidebar slide-in dari kiri dengan smooth animation
- [ ] Overlay backdrop muncul saat sidebar terbuka
- [ ] Close button di sidebar berfungsi
- [ ] Klik di luar sidebar menutup menu
- [ ] Navigation items menutup sidebar saat diklik
- [ ] Cards menggantikan tables
- [ ] Text tetap readable (tidak terlalu kecil)
- [ ] Buttons tidak overflow
- [ ] Forms mudah diisi
- [ ] Export buttons compact dengan label singkat
- [ ] Search dan filter forms full width

### ✅ Tablet View (640px - 1024px)
- [ ] Hamburger menu masih terlihat
- [ ] Grid layout 2-3 kolom di dashboard
- [ ] Spacing lebih lapang dari mobile
- [ ] Text size lebih besar
- [ ] Buttons ukuran normal
- [ ] Cards masih digunakan untuk tables

### ✅ Desktop View (≥ 1024px)
- [ ] Sidebar static/visible tanpa hamburger
- [ ] Tables ditampilkan (bukan cards)
- [ ] Grid layout 3 kolom penuh
- [ ] Spacing optimal
- [ ] Full labels di buttons
- [ ] Hover effects berfungsi

---

## Test Scenarios by Page

### 1. Dashboard (`/admin/dashboard`)

#### Mobile Test:
```
✓ Stats cards stacked vertikal (1 kolom)
✓ Font numbers: 2xl (bukan 3xl)
✓ Padding cards: p-4 (bukan p-6)
✓ Sub-cards (Reinforce/Backplate/CompBase) responsive
✓ Recent movements table compact
✓ Badge size kecil dengan padding minimal
```

#### Desktop Test:
```
✓ Stats cards 2 kolom untuk "Troli Masuk/Keluar"
✓ Duration cards 3 kolom
✓ Font numbers: 3xl
✓ Padding cards: p-6
✓ Table dengan spacing normal
```

### 2. History (`/admin/history/index`)

#### Mobile Test:
```
✓ Table TIDAK terlihat
✓ Card layout terlihat dengan info lengkap
✓ Export buttons dengan label "CSV" dan "XLSX" (tanpa "Export")
✓ Filter form full width
✓ Status badges compact
✓ Sequence number di header card
✓ Timestamp di footer card
```

#### Desktop Test:
```
✓ Table terlihat dengan 11 kolom
✓ Card layout TIDAK terlihat
✓ Export buttons dengan label lengkap "Export CSV"
✓ Filter form inline
✓ Horizontal scroll jika diperlukan
```

### 3. Approvals (`/admin/approvals/index`)

#### Mobile Test:
```
✓ Table TIDAK terlihat
✓ Card layout untuk setiap user
✓ Status filter buttons wrap ke baris baru
✓ Detail button full width di card
✓ User name sebagai heading card
```

#### Desktop Test:
```
✓ Table terlihat dengan 5 kolom
✓ Card layout TIDAK terlihat
✓ Status filter buttons inline
✓ Detail button di kolom kanan
```

### 4. Trolleys (`/trolleys/index`)

#### Mobile Test:
```
✓ Export buttons compact
✓ "Tambah Troli" button dengan label "Tambah"
✓ Search input full width
✓ Status select full width
✓ Cari button full width
✓ Form stacked vertikal
✓ Table scroll horizontal dengan min-width
```

#### Desktop Test:
```
✓ Export buttons dengan label lengkap
✓ "Tambah Troli" button dengan label penuh
✓ Search input, status, dan button inline
✓ Form horizontal
✓ Table full width tanpa scroll
```

---

## Browser DevTools Testing

### Chrome DevTools:
1. Open DevTools: `F12` atau `Ctrl+Shift+I`
2. Toggle Device Toolbar: `Ctrl+Shift+M`
3. Test preset devices:

```
iPhone SE       : 375 x 667
iPhone 12 Pro   : 390 x 844
iPhone 14 Pro Max: 430 x 932
Pixel 5         : 393 x 851
Samsung Galaxy S20: 360 x 800
iPad Mini       : 768 x 1024
iPad Air        : 820 x 1180
iPad Pro        : 1024 x 1366
```

### Custom Breakpoints:
```
320px  - Very small phone
375px  - iPhone SE
390px  - iPhone 12+
640px  - Tailwind 'sm'
768px  - Tailwind 'md'
1024px - Tailwind 'lg'
1280px - Tailwind 'xl'
```

---

## Manual Testing Steps

### Step 1: Sidebar Navigation
```
1. Resize browser ke < 1024px
2. Verify hamburger icon muncul di header kiri
3. Klik hamburger icon
4. Verify sidebar slide-in dari kiri
5. Verify overlay backdrop muncul
6. Verify close button (X) di sidebar
7. Klik salah satu menu
8. Verify sidebar tertutup otomatis
9. Buka sidebar lagi
10. Klik di overlay backdrop
11. Verify sidebar tertutup
```

### Step 2: Dashboard Cards
```
1. Resize ke 375px (mobile)
2. Verify cards stacked vertikal
3. Verify numbers tidak overflow
4. Verify sub-cards (Reinforce/etc) responsive
5. Resize ke 768px (tablet)
6. Verify cards dalam grid 2-3 kolom
7. Resize ke 1024px+ (desktop)
8. Verify layout optimal
```

### Step 3: Tables vs Cards
```
1. Buka /admin/history/index
2. Resize ke 375px
3. Verify table HIDDEN, cards VISIBLE
4. Scroll cards, verify mudah dibaca
5. Resize ke 1024px+
6. Verify cards HIDDEN, table VISIBLE
7. Repeat untuk /admin/approvals/index
```

### Step 4: Forms & Filters
```
1. Buka /trolleys/index
2. Resize ke 375px
3. Verify search input full width
4. Verify status select full width
5. Verify buttons full width atau wrapped
6. Isi form dan submit
7. Verify hasil search berfungsi
```

### Step 5: Buttons & Actions
```
1. Test semua export buttons
2. Verify icons tetap visible
3. Verify text adjust di mobile
4. Verify buttons tidak terpotong
5. Test touch target (minimal 44x44px)
```

---

## Real Device Testing

### iOS Testing:
```
Safari iOS 13+
- Test di iPhone SE/8
- Test di iPhone 12/13/14
- Test di iPad
- Test landscape & portrait
- Test Safari gestures (swipe)
```

### Android Testing:
```
Chrome Android 8+
- Test di Samsung Galaxy
- Test di Google Pixel
- Test di berbagai screen size
- Test landscape & portrait
```

---

## Common Issues to Check

### ❌ Red Flags:
- [ ] Horizontal scroll di viewport penuh
- [ ] Text terlalu kecil untuk dibaca (< 12px)
- [ ] Buttons terlalu kecil untuk touch (< 44px)
- [ ] Overflow text tanpa ellipsis
- [ ] Layout broken di breakpoint tertentu
- [ ] Forms tidak bisa disubmit di mobile
- [ ] Images tidak responsive
- [ ] Fixed width elements overflow

### ✅ Good Signs:
- [x] Smooth transitions & animations
- [x] Touch-friendly button sizes
- [x] Readable typography
- [x] No horizontal scroll
- [x] Forms mudah diisi
- [x] Cards mudah dibaca
- [x] Proper spacing & padding
- [x] Consistent behavior across devices

---

## Performance Testing

### Lighthouse Mobile Audit:
```bash
1. Open Chrome DevTools
2. Go to Lighthouse tab
3. Select "Mobile" device
4. Select "Performance" + "Accessibility"
5. Click "Generate Report"
6. Target scores:
   - Performance: > 90
   - Accessibility: > 90
   - Best Practices: > 90
```

### Network Throttling:
```
1. DevTools > Network tab
2. Select "Slow 3G" or "Fast 3G"
3. Test page loading
4. Verify progressive loading
5. Check for layout shifts (CLS)
```

---

## Automated Testing (Optional)

### Responsive Screenshots:
```bash
# Using Playwright/Puppeteer
npm install -D @playwright/test

# Create test file: tests/responsive.spec.js
test('should be responsive', async ({ page }) => {
  const viewports = [
    { width: 375, height: 667 },   // iPhone SE
    { width: 768, height: 1024 },  // iPad
    { width: 1920, height: 1080 }, // Desktop
  ];

  for (const viewport of viewports) {
    await page.setViewportSize(viewport);
    await page.goto('/admin/dashboard');
    await page.screenshot({
      path: `screenshots/${viewport.width}x${viewport.height}.png`
    });
  }
});
```

---

## Regression Testing

### Before Deploy Checklist:
```
✓ Test semua breakpoints (320, 375, 640, 768, 1024, 1280)
✓ Test semua pages utama
✓ Test di Chrome, Firefox, Safari
✓ Test di real Android & iOS device
✓ Test touch interactions
✓ Test form submissions
✓ Test table scrolling
✓ Lighthouse audit score > 90
✓ No console errors
✓ No layout shifts (CLS)
```

---

## Bug Report Template

Jika menemukan bug:

```markdown
**Browser**: Chrome 120 / Safari 17 / Firefox 121
**Device**: iPhone 14 Pro / Samsung S23 / Desktop
**Screen Size**: 390 x 844
**URL**: /admin/history/index

**Issue**:
Describe the issue...

**Steps to Reproduce**:
1. Go to...
2. Click on...
3. Scroll to...

**Expected**:
What should happen...

**Actual**:
What actually happened...

**Screenshot**:
[Attach screenshot]
```

---

## Useful DevTools Shortcuts

```
Ctrl+Shift+M    - Toggle device toolbar
Ctrl+Shift+C    - Inspect element
Ctrl+Shift+P    - Command palette
Ctrl+Shift+I    - Open DevTools
Ctrl+R          - Reload page
Ctrl+Shift+R    - Hard reload
F12             - Toggle DevTools
```

---

## Resources

- [Can I Use](https://caniuse.com/) - Check browser support
- [Responsive Checker](https://responsivedesignchecker.com/) - Online tool
- [BrowserStack](https://www.browserstack.com/) - Real device testing
- [Chrome DevTools Docs](https://developer.chrome.com/docs/devtools/)

---

**Last Updated**: 2024
**Status**: Ready for Testing
**Next Review**: After major UI changes