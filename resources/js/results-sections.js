/**
 * AI Results Section Generator
 * Fetches personalized content for each results section and renders markdown.
 */

window.renderAIMarkdown = function(container, markdown, themeClass) {
  if (!markdown) return;
  var lines = markdown.replace(/\r/g, '').split('\n');
  var html = '';
  var i = 0;

  while (i < lines.length) {
    var line = lines[i];
    var trimmed = line.trim();
    if (!trimmed) { i++; continue; }

    // ### Headings (handle with or without space after #)
    if (/^#{1,4}/.test(trimmed)) {
      var hashes = trimmed.match(/^(#+)/)[1];
      var level = Math.min(hashes.length, 4);
      var tag = 'h' + (level + 2);
      var headingText = trimmed.replace(/^#+\s*/, '');
      headingText = processInline(headingText);
      html += '<' + tag + ' class="font-semibold text-warm-800 mt-5 mb-2">' + headingText + '</' + tag + '>';
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
      html += '<div class="flex gap-2.5 my-1.5 pl-1"><span class="text-' + themeClass + '-400 shrink-0 mt-0.5 text-sm">•</span><span class="text-sm text-warm-600 leading-relaxed">' + processInline(bulletText) + '</span></div>';
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
      html += '<div class="flex gap-2.5 my-1.5 pl-1"><span class="text-' + themeClass + '-400 shrink-0 mt-0.5 text-sm">•</span><span class="text-sm text-warm-600 leading-relaxed">' + processInline(dashText) + '</span></div>';
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
      html += '<div class="flex gap-3 my-1.5"><span class="text-' + themeClass + '-500 font-semibold text-sm shrink-0 mt-0.5">' + num + '.</span><span class="text-sm text-warm-600 leading-relaxed">' + processInline(numText) + '</span></div>';
      continue;
    }

    // Plain paragraph
    html += '<p class="text-sm text-warm-600 leading-relaxed mb-2">' + processInline(trimmed) + '</p>';
    i++;
  }

  // Wrap in container
  html = '<div class="space-y-1">' + html + '</div>' +
         '<p class="text-xs text-warm-400 italic mt-4 pt-3 border-t border-warm-100">AI-generated information, not a diagnosis. A professional can give you a proper evaluation.</p>';
  container.innerHTML = html;
};

window.processInline = function(text) {
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

// Geolocation helper — gets user's city/area for location-aware recommendations
window.getUserLocation = function() {
  return new Promise(function(resolve) {
    if (!navigator.geolocation) { resolve(null); return; }
    navigator.geolocation.getCurrentPosition(
      function(pos) {
        // Reverse geocode using Nominatim
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude + '&zoom=10')
          .then(function(r) { return r.json(); })
          .then(function(data) {
            var addr = data.address || {};
            var city = addr.city || addr.town || addr.village || addr.county || '';
            var country = addr.country || '';
            var state = addr.state || '';
            var loc = [city, state, country].filter(Boolean).join(', ');
            resolve(loc || null);
          })
          .catch(function() { resolve(null); });
      },
      function() { resolve(null); },
      { timeout: 8000, maximumAge: 600000 }
    );
  });
};
