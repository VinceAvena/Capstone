/* ==========================================================================
   TindaPresyo front-end script (plain JavaScript, no libraries).
   Each block runs only if the page contains the element it needs.
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {

  // 1. Mobile menu button ---------------------------------------------------
  var toggle = document.getElementById('nav-toggle');
  var nav = document.getElementById('main-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // 2. Confirmation pop-up for risky buttons: <form data-confirm="Are you sure?"> ----
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) { e.preventDefault(); }
    });
  });

  // 3. Stop double submissions: disable the submit button after the first click ----
  document.querySelectorAll('form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (e.defaultPrevented) { return; }
      form.querySelectorAll('button[type=submit]').forEach(function (btn) {
        setTimeout(function () { btn.disabled = true; btn.textContent = 'Please wait...'; }, 0);
      });
    });
  });

  // 4. Registration form: show stall fields only for vendors ----------------------
  var accountType = document.getElementById('account_type');
  var stallFields = document.getElementById('stall-fields');
  if (accountType && stallFields) {
    var syncType = function () { stallFields.style.display = accountType.value === 'vendor' ? 'block' : 'none'; };
    accountType.addEventListener('change', syncType);
    syncType();
  }

  // 5. Price posting form: highlight rows the vendor has changed ---------------------
  document.querySelectorAll('input.price-input[data-original]').forEach(function (input) {
    input.addEventListener('input', function () {
      var row = input.closest('tr');
      var changed = input.value !== '' && parseFloat(input.value) !== parseFloat(input.getAttribute('data-original'));
      if (row) { row.classList.toggle('changed', changed); }
    });
  });

  // 6. Complaint form ------------------------------------------------------------------
  //    Choosing a stall loads its posted prices; choosing a commodity shows the
  //    posted price and the overcharge as the shopper types what they paid.
  var stallSelect = document.getElementById('stall_id');
  var commoditySelect = document.getElementById('commodity_id');
  var reported = document.getElementById('reported_price');
  var infoBox = document.getElementById('price-info');
  var prices = [];

  function peso(n) { return '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

  function renderInfo() {
    if (!infoBox || !commoditySelect) { return; }
    var item = prices.find(function (p) { return String(p.commodity_id) === commoditySelect.value; });
    if (!item) { infoBox.innerHTML = ''; return; }
    var html = '<div class="alert info">Posted price: <strong>' + peso(item.price) + '</strong> per ' + item.unit +
               ' (posted ' + item.date + ').';
    var paid = parseFloat(reported && reported.value);
    if (!isNaN(paid)) {
      var diff = paid - item.price;
      html += diff > 0
        ? ' You report paying <strong class="up">' + peso(diff) + ' more</strong> (' + (diff / item.price * 100).toFixed(1) + '% over).'
        : ' <strong>The price you paid is not higher than the posted price, so it cannot be filed as overpricing.</strong>';
    }
    infoBox.innerHTML = html + '</div>';
  }

  function loadPrices(keepValue) {
    if (!stallSelect || !commoditySelect) { return; }
    var stallId = stallSelect.value;
    commoditySelect.innerHTML = '<option value="">Choose a stall first</option>';
    prices = [];
    renderInfo();
    if (!stallId) { return; }

    commoditySelect.innerHTML = '<option value="">Loading...</option>';
    fetch(stallSelect.getAttribute('data-url').replace('__ID__', stallId), { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (rows) {
        prices = rows;
        if (!rows.length) {
          commoditySelect.innerHTML = '<option value="">This stall has no posted prices yet</option>';
          return;
        }
        commoditySelect.innerHTML = '<option value="">Choose the commodity</option>' + rows.map(function (p) {
          return '<option value="' + p.commodity_id + '">' + p.name + ' (' + peso(p.price) + ' / ' + p.unit + ')</option>';
        }).join('');
        if (keepValue) { commoditySelect.value = keepValue; }
        renderInfo();
      })
      .catch(function () {
        commoditySelect.innerHTML = '<option value="">Could not load prices. Please reload the page.</option>';
      });
  }

  if (stallSelect && commoditySelect) {
    stallSelect.addEventListener('change', function () { loadPrices(null); });
    commoditySelect.addEventListener('change', renderInfo);
    if (reported) { reported.addEventListener('input', renderInfo); }
    if (stallSelect.value) { loadPrices(commoditySelect.getAttribute('data-old')); }
  }

  // 7. Photo preview before upload -----------------------------------------------------------
  var photo = document.getElementById('photo');
  var preview = document.getElementById('photo-preview');
  if (photo && preview) {
    photo.addEventListener('change', function () {
      var file = photo.files && photo.files[0];
      if (!file) { preview.innerHTML = ''; return; }
      var maxKb = parseInt(photo.getAttribute('data-max-kb'), 10) || 4096;
      if (file.size / 1024 > maxKb) {
        preview.innerHTML = '<div class="alert error">That photo is larger than ' + (maxKb / 1024) + ' MB. Please choose a smaller one.</div>';
        photo.value = '';
        return;
      }
      var reader = new FileReader();
      reader.onload = function (e) { preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview of the chosen photo" style="max-height:220px;border-radius:10px;border:1px solid #e4ddcc">'; };
      reader.readAsDataURL(file);
    });
  }

  // 8. "Select all" checkboxes: <input type="checkbox" data-select-all="group"> ------------------
  document.querySelectorAll('input[data-select-all]').forEach(function (master) {
    master.addEventListener('change', function () {
      document.querySelectorAll('input[data-group="' + master.getAttribute('data-select-all') + '"]').forEach(function (box) { box.checked = master.checked; });
    });
  });

  // 9. Print button -----------------------------------------------------------------------------------
  document.querySelectorAll('[data-print]').forEach(function (btn) {
    btn.addEventListener('click', function () { window.print(); });
  });
});
