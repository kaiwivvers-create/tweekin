/**
 * AI Results Section Generator
 * Fetches personalized content for each results section and renders markdown.
 */

/**
 * Shared markdown renderer for AI output (sections, chat replies, overviews).
 * Understands the subset Gemini is asked to produce: ### headings, * / - bullets,
 * 1. numbered items, > quotes, --- dividers, **bold**, *italic* and `code`.
 *
 * @param  {string} markdown   raw model output
 * @param  {string} themeClass 'physical' | 'mental' — drives the accent color
 * @param  {string} [textClass] body text color class (defaults to warm-600)
 * @return {string} HTML string
 */
window.renderMarkdown = function(markdown, themeClass, textClass) {
  if (!markdown) return '';
  var body = textClass || 'text-warm-600';
  var lines = String(markdown).replace(/\r/g, '').split('\n');
  var html = '';
  var i = 0;

  while (i < lines.length) {
    var line = lines[i];
    var trimmed = line.trim();
    if (!trimmed) { i++; continue; }

    // Headings: #, ##, ###, ... (with or without a space after the hashes).
    // The prompt asks for ###, so that level reads as the main section heading.
    if (/^#{1,6}/.test(trimmed)) {
      var level = trimmed.match(/^(#+)/)[1].length;
      var headingText = processInline(trimmed.replace(/^#+\s*/, '').replace(/\s*#+\s*$/, ''));
      var tag = level <= 2 ? 'h3' : (level === 3 ? 'h4' : 'h5');
      var headingClass = level <= 2
        ? 'font-display font-bold text-base text-warm-800 mt-5 mb-2 first:mt-0'
        : (level === 3
          ? 'font-semibold text-sm text-warm-800 mt-4 mb-1.5 first:mt-0'
          : 'font-semibold text-[13px] text-warm-700 mt-3 mb-1 uppercase tracking-wide first:mt-0');
      html += '<' + tag + ' class="' + headingClass + '">' + headingText + '</' + tag + '>';
      i++; continue;
    }

    // --- dividers
    if (/^---+\s*$/.test(trimmed)) {
      html += '<div class="my-5 border-t border-warm-100"></div>';
      i++; continue;
    }

    // > blockquotes
    if (/^>\s+/.test(trimmed)) {
      var bqText = trimmed.substring(2);
      i++;
      while (i < lines.length) {
        var next = lines[i].trim();
        if (!next || /^(>\s|#{1,4}\s|---|\*\s|-\s|\d+\.)/.test(next)) break;
        bqText += ' ' + next; i++;
      }
      html += '<blockquote class="text-sm text-warm-500 italic border-l-2 border-warm-200 pl-3 py-1 my-3 rounded-r-lg bg-warm-50/50">' + processInline(bqText) + '</blockquote>';
      continue;
    }

    // * bullets (single asterisk only, not **bold or ***bold italic)
    if (/^\*(?!\*)\s*/.test(trimmed)) {
      var bulletText = trimmed.replace(/^\*\s*/, '');
      i++;
      while (i < lines.length) {
        var next = lines[i].trim();
        if (!next || /^(#{1,4}\s|---|\*\s|-\s|\d+\.|>)/.test(next)) break;
        bulletText += ' ' + next; i++;
      }
      html += '<div class="flex gap-2.5 my-1.5 pl-1 first:mt-0"><span class="text-' + themeClass + '-400 shrink-0 mt-0.5 text-sm">•</span><span class="text-sm ' + body + ' leading-relaxed">' + processInline(bulletText) + '</span></div>';
      continue;
    }

    // - dashes as bullets (with or without space)
    if (/^-+\s*/.test(trimmed)) {
      var dashText = trimmed.replace(/^-+\s*/, '');
      i++;
      while (i < lines.length) {
        var next = lines[i].trim();
        if (!next || /^(#{1,4}\s|---|\*\s|-\s|\d+\.|>)/.test(next)) break;
        dashText += ' ' + next; i++;
      }
      html += '<div class="flex gap-2.5 my-1.5 pl-1 first:mt-0"><span class="text-' + themeClass + '-400 shrink-0 mt-0.5 text-sm">•</span><span class="text-sm ' + body + ' leading-relaxed">' + processInline(dashText) + '</span></div>';
      continue;
    }

    // 1. numbered items
    if (/^\d+\.\s+/.test(trimmed)) {
      var num = trimmed.match(/^(\d+)\.\s+/)[1];
      var numText = trimmed.replace(/^\d+\.\s+/, '');
      i++;
      while (i < lines.length) {
        var next = lines[i].trim();
        if (!next || /^(#{1,4}\s|---|\*\s|-\s|\d+\.|>)/.test(next)) break;
        numText += ' ' + next; i++;
      }
      html += '<div class="flex gap-3 my-1.5 first:mt-0"><span class="text-' + themeClass + '-500 font-semibold text-sm shrink-0 mt-0.5">' + num + '.</span><span class="text-sm ' + body + ' leading-relaxed">' + processInline(numText) + '</span></div>';
      continue;
    }

    // Plain paragraph
    html += '<p class="text-sm ' + body + ' leading-relaxed mb-2">' + processInline(trimmed) + '</p>';
    i++;
  }

  return '<div class="space-y-1">' + html + '</div>';
};

/**
 * Render markdown into a container, with the standard AI disclaimer.
 *
 * @param {object} [options] { disclaimer: false } to omit the footer,
 *                           { text: 'text-mental-700' } to override body color.
 */
window.renderAIMarkdown = function(container, markdown, themeClass, options) {
  if (!container || !markdown) return;
  options = options || {};
  var disclaimer = options.disclaimer === false
    ? ''
    : '<p class="text-xs text-warm-400 italic mt-4 pt-3 border-t border-warm-100">AI-generated information, not a diagnosis. A professional can give you a proper evaluation.</p>';
  container.innerHTML = window.renderMarkdown(markdown, themeClass, options.text) + disclaimer;
};

window.processInline = function(text) {
  // Escape first so model output can never inject markup
  text = window.escapeHtml(text);
  // Handle ***bold italic*** first
  text = text.replace(/\*\*\*(.*?)\*\*\*/g, '<strong><em>$1</em></strong>');
  // Handle **bold**
  text = text.replace(/\*\*(.*?)\*\*/g, '<strong class="text-warm-800">$1</strong>');
  // Handle *italic* (single asterisks not part of bold)
  text = text.replace(/\*([^*]+?)\*/g, '<em>$1</em>');
  // Handle `inline code`
  text = text.replace(/`([^`]+)`/g, '<code class="px-1.5 py-0.5 rounded bg-warm-100 text-warm-700 text-xs font-mono">$1</code>');
  return text;
};

window.fetchAISection = function(section, context, csrfToken, themeClass) {
  var container = document.getElementById('section-' + section);
  if (!container) return Promise.resolve();
  var contentDiv = container.querySelector('.ai-section-content');
  if (!contentDiv) return Promise.resolve();

  return fetch('/api/generate-section', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ section: section, context: context })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data.content) {
      renderAIMarkdown(contentDiv, data.content, themeClass);
    } else {
      contentDiv.innerHTML = '<p class="text-sm text-warm-500 italic">Could not load this section. Try refreshing.</p>';
    }
  })
  .catch(function() {
    contentDiv.innerHTML = '<p class="text-sm text-warm-500 italic">Something went wrong. Try refreshing.</p>';
  });
};

// ---------- Nearby care carousel ----------

window.escapeHtml = function(str) {
  return String(str == null ? '' : str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
};

function nearbySpinner(theme, label) {
  return '<div class="flex flex-col items-center justify-center py-8 gap-3">' +
    '<div class="relative w-10 h-10">' +
    '<div class="absolute inset-0 rounded-full border-[3px] border-' + theme + '-200 border-t-' + theme + '-500 animate-spin" style="animation-duration: 0.9s;"></div>' +
    '<div class="absolute inset-1 rounded-full border-[3px] border-warm-200 border-b-warm-400 animate-spin" style="animation-duration: 1.2s; animation-direction: reverse;"></div>' +
    '</div><p class="text-xs text-warm-400 font-medium">' + escapeHtml(label) + '</p></div>';
}

function mapsSearchUrl(name, location) {
  var q = name + (location ? ' near ' + location : '');
  return 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(q);
}

function directionsUrl(f) {
  if (f.mapsUrl) {
    return f.mapsUrl;
  }
  if (f.lat != null && f.lon != null) {
    return 'https://www.google.com/maps/dir/?api=1&destination=' + f.lat + ',' + f.lon;
  }
  return mapsSearchUrl(f.name + (f.address ? ' ' + f.address : ''), '');
}

function cardPhoto(f, theme) {
  var badge = '<span class="absolute top-3 right-3 text-[10px] uppercase tracking-wide font-semibold px-2 py-0.5 rounded-full bg-white/90 text-warm-700 backdrop-blur-sm">' + escapeHtml(f.type) + '</span>';

  if (f.image) {
    return '<div class="relative h-32 w-full overflow-hidden bg-' + theme + '-100">' +
      '<img src="' + escapeHtml(f.image) + '" alt="' + escapeHtml(f.name) + '" loading="lazy" referrerpolicy="no-referrer"' +
        ' class="w-full h-full object-cover" onerror="this.style.display=\'none\'">' +
      '<div class="absolute inset-0 bg-gradient-to-t from-black/25 to-transparent"></div>' +
      badge +
      '</div>';
  }

  return '<div class="relative h-32 w-full bg-gradient-to-br from-' + theme + '-100 to-' + theme + '-200 flex items-center justify-center">' +
    '<svg class="w-8 h-8 text-' + theme + '-500 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>' +
    badge +
    '</div>';
}

function facilityCard(f, theme, location) {
  var call = (f.call || f.phone || '').trim();

  var callBtn = call
    ? '<a href="tel:' + escapeHtml(call.replace(/[^0-9+]/g, '')) + '" class="flex items-center justify-center gap-2 flex-1 py-2.5 rounded-xl bg-' + theme + '-400 hover:bg-' + theme + '-500 text-white text-xs font-semibold transition-colors">' +
        '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>' +
        '<span>Call</span></a>'
    : '';

  var meta = [];
  if (f.distanceKm != null) meta.push(f.distanceKm + ' km away');
  if (f.address) meta.push(f.address);

  var rating = (f.rating != null)
    ? '<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-warm-600">' +
        '<svg class="w-3 h-3 text-physical-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.3 4a1 1 0 00.95.69h4.2c.97 0 1.37 1.24.59 1.81l-3.4 2.47a1 1 0 00-.36 1.12l1.3 4c.3.92-.76 1.69-1.54 1.12l-3.4-2.47a1 1 0 00-1.18 0l-3.4 2.47c-.78.57-1.84-.2-1.54-1.12l1.3-4a1 1 0 00-.36-1.12L2.01 9.43c-.78-.57-.38-1.81.59-1.81h4.2a1 1 0 00.95-.69l1.3-4z"/></svg>' +
        f.rating.toFixed(1) + '</span>' +
        ((f.ratingCount != null) ? '<span class="text-[11px] text-warm-400">(' + f.ratingCount + ')</span>' : '')
    : '';

  return '<article class="snap-start shrink-0 w-[272px] sm:w-[310px] bg-white border border-warm-200 rounded-2xl overflow-hidden flex flex-col transition-shadow hover:shadow-md">' +
    cardPhoto(f, theme) +
    '<div class="p-4 flex flex-col flex-1">' +
      '<div class="flex items-start justify-between gap-2">' +
        '<h3 class="font-semibold text-warm-800 text-sm leading-snug">' + escapeHtml(f.name) + '</h3>' +
        (rating ? '<span class="shrink-0 flex items-center gap-1 mt-0.5">' + rating + '</span>' : '') +
      '</div>' +
      (meta.length ? '<p class="text-[11px] text-warm-400 mt-1 leading-snug">' + escapeHtml(meta.join(' · ')) + '</p>' : '') +
      (f.goodFor ? '<p class="text-xs text-warm-600 leading-relaxed mt-3"><span class="font-semibold text-warm-700">Good for:</span> ' + escapeHtml(f.goodFor) + '</p>' : '') +
      (f.why ? '<p class="text-[11px] text-warm-500 italic leading-relaxed mt-auto pt-3 border-t border-warm-100">' + escapeHtml(f.why) + '</p>' : '') +
      '<div class="flex gap-2 mt-3">' + callBtn +
        '<a href="' + escapeHtml(directionsUrl(f)) + '" target="_blank" rel="noopener" class="flex items-center justify-center gap-1.5 flex-1 py-2.5 rounded-xl border border-' + theme + '-200 text-' + theme + '-700 hover:bg-' + theme + '-50 text-xs font-semibold transition-colors">' +
          '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>' +
          '<span>Directions</span></a>' +
      '</div>' +
    '</div>' +
  '</article>';
}

function helplineCard(h, theme) {
  var digits = String(h.number || '').replace(/[^0-9+]/g, '');
  return '<a href="tel:' + escapeHtml(digits) + '" class="flex items-center gap-3 p-3.5 rounded-xl border border-' + theme + '-200 bg-' + theme + '-50 hover:bg-' + theme + '-100 transition-colors">' +
    '<div class="w-9 h-9 rounded-full bg-' + theme + '-400 text-white flex items-center justify-center shrink-0">' +
      '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>' +
    '</div>' +
    '<div class="min-w-0 flex-1">' +
      '<p class="text-sm font-semibold text-warm-800 truncate">' + escapeHtml(h.name) + '</p>' +
      '<p class="text-[11px] text-warm-500 leading-snug">' + escapeHtml(h.note) + '</p>' +
    '</div>' +
    '<span class="text-sm font-bold text-' + theme + '-700 whitespace-nowrap">' + escapeHtml(h.number) + '</span>' +
  '</a>';
}

window.renderNearbyCare = function(container, data, theme, location) {
  var facilities = (data && data.facilities) || [];
  var helplines = (data && data.helplines) || [];
  // The server may have resolved a coarse network location when the browser
  // would not give us one, so trust its label over what we sent up.
  var label = (data && data.location) || location || null;
  var approximate = (data && data.locationSource) === 'ip';
  var html = '';

  if (facilities.length > 0) {
    var cards = facilities.map(function(f) { return facilityCard(f, theme, location); }).join('');
    html += '<div class="relative">' +
      '<button type="button" data-nc-prev aria-label="Previous" class="hidden sm:flex absolute -left-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 items-center justify-center rounded-full bg-white border border-warm-200 shadow-sm text-warm-500 hover:text-warm-700 transition-colors">' +
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>' +
      '<div data-nc-track class="flex gap-4 overflow-x-auto snap-x snap-mandatory pb-2 no-scrollbar scroll-smooth">' + cards + '</div>' +
      '<button type="button" data-nc-next aria-label="Next" class="hidden sm:flex absolute -right-3 top-1/2 -translate-y-1/2 z-10 w-8 h-8 items-center justify-center rounded-full bg-white border border-warm-200 shadow-sm text-warm-500 hover:text-warm-700 transition-colors">' +
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>' +
      '</div>' +
      '<p class="text-[11px] text-warm-400 mt-1">' +
        'Sorted by how well each one fits what you described. Swipe or use the arrows to see more.' +
        (label
          ? ' Searched within about 20 km of ' + escapeHtml(label)
            + (approximate ? ' — an approximate area from your connection.' : '.')
          : '') +
        '</p>';
  } else {
    html += '<div class="flex items-start gap-3 p-4 rounded-xl border border-warm-200 bg-warm-50">' +
      '<div class="w-8 h-8 rounded-lg bg-warm-100 flex items-center justify-center shrink-0 mt-0.5">' +
        '<svg class="w-4 h-4 text-warm-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
      '</div>' +
      '<div class="text-sm text-warm-600 leading-relaxed">' +
        '<p class="font-semibold text-warm-700 mb-1">There\'s no nearby care we could pin down as suitable</p>' +
        '<p>' + (label
          ? 'We searched around ' + escapeHtml(label) + ' and could not find a facility that matches what you described.'
          : 'We could not get your location, so we could not search your area.') +
        ' The numbers below are the fastest way to reach someone who can point you in the right direction.</p>' +
      '</div>' +
    '</div>';
  }

  if (helplines.length > 0) {
    html += '<div class="mt-5">' +
      '<h3 class="text-[11px] font-semibold uppercase tracking-wide text-warm-400 mb-3">' +
        (facilities.length > 0 ? 'Rather talk to someone now?' : 'Call someone who can help') +
      '</h3>' +
      '<div class="grid sm:grid-cols-2 gap-3">' + helplines.map(function(h) { return helplineCard(h, theme); }).join('') + '</div>' +
      '<p class="text-[11px] text-warm-400 mt-3">Numbers are general public lines — confirm them when you call. In an emergency, contact your local emergency number.</p>' +
    '</div>';
  }

  if (!label) {
    html += '<button type="button" data-nc-retry class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-' + theme + '-700 hover:underline">' +
      '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' +
      'Use my location for local results</button>';
  }

  container.innerHTML = html;

  var track = container.querySelector('[data-nc-track]');
  if (track) {
    var step = function() {
      var card = track.querySelector('article');
      return card ? card.offsetWidth + 16 : 300;
    };
    var prev = container.querySelector('[data-nc-prev]');
    var next = container.querySelector('[data-nc-next]');
    if (prev) prev.addEventListener('click', function() { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function() { track.scrollBy({ left: step(), behavior: 'smooth' }); });
  }

  var retry = container.querySelector('[data-nc-retry]');
  if (retry) {
    retry.addEventListener('click', function() {
      container.innerHTML = nearbySpinner(theme, 'Detecting your location...');
      getUserLocation().then(function(loc) {
        window.fetchNearbyCare(loc, window._nearbyCareContext || {}, window._nearbyCareCsrf, theme);
      });
    });
  }
};

window.fetchNearbyCare = function(location, context, csrfToken, theme) {
  var container = document.getElementById('nearby-care-body');
  if (!container) return;

  window._nearbyCareContext = context;
  window._nearbyCareCsrf = csrfToken;

  var label = location && location.label ? location.label : null;
  var lat = location && location.lat != null ? location.lat : null;
  var lon = location && location.lon != null ? location.lon : null;

  if (label) context = Object.assign({}, context, { location: label });

  container.innerHTML = nearbySpinner(theme, label
    ? 'Searching real facilities near ' + label + '...'
    : 'Checking your area for real facilities...');

  fetch('/api/nearby-care', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ context: context, location: label, lat: lat, lon: lon })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data && (data.facilities || data.helplines)) {
      renderNearbyCare(container, data, theme, label);
    } else {
      container.innerHTML = '<p class="text-sm text-warm-500 italic">Could not load care options right now. Try refreshing.</p>';
    }
  })
  .catch(function() {
    container.innerHTML = '<p class="text-sm text-warm-500 italic">Could not load care options right now. Try refreshing.</p>';
  });
};

// Geolocation helper — returns { label, lat, lon } for location-aware recommendations
window.getUserLocation = function() {
  return new Promise(function(resolve) {
    if (!navigator.geolocation) { resolve(null); return; }
    navigator.geolocation.getCurrentPosition(
      function(pos) {
        var coords = { lat: pos.coords.latitude, lon: pos.coords.longitude };

        // Reverse geocode using Nominatim for a human-readable label
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + coords.lat + '&lon=' + coords.lon + '&zoom=10')
          .then(function(r) { return r.json(); })
          .then(function(data) {
            var addr = data.address || {};
            var city = addr.city || addr.town || addr.village || addr.county || '';
            var state = addr.state || '';
            var country = addr.country || '';
            var label = [city, state, country].filter(Boolean).join(', ');
            resolve({ label: label || null, lat: coords.lat, lon: coords.lon });
          })
          .catch(function() { resolve({ label: null, lat: coords.lat, lon: coords.lon }); });
      },
      function() { resolve(null); },
      { timeout: 8000, maximumAge: 600000 }
    );
  });
};
