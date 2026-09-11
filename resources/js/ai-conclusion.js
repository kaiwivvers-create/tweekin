/**
 * AI Conclusion Chat Component
 * Reads config from data-ai-config attribute on the container div.
 */
document.addEventListener('DOMContentLoaded', function () {
  var containers = document.querySelectorAll('[data-ai-config]');
  containers.forEach(function (container) {
    var config;
    try {
      config = JSON.parse(container.getAttribute('data-ai-config'));
    } catch (e) {
      return;
    }

    var type = config.type;
    var geminiConfigured = config.geminiConfigured;
    var context = config.context;
    var chatUrl = config.chatUrl;
    var userAvatarUrl = config.userAvatarUrl || '';
    var themeClass = config.themeClass;

    var chat = container.querySelector('.ai-chat-messages');
    var input = container.querySelector('.ai-chat-input');
    var sendBtn = container.querySelector('.ai-chat-send');
    if (!chat || !input || !sendBtn) return;

    var history = [];

    // -- Fallback responses --
    var fallbacks = {
      mental: {
        default:
          "Based on what you shared, there are several things that could be happening. Stress, anxiety, OCD patterns, and social discomfort are all very common and very treatable.\n\n" +
          "**For stress/burnout**: Set boundaries, take short breaks every 90 minutes, and make sure you are sleeping enough.\n\n" +
          "**For intrusive thoughts**: Label them as 'just thoughts' and let them pass without engaging. Do not fight them.\n\n" +
          "**For social anxiety**: Start small with one brief interaction per day. Most people are too worried about themselves to judge you.\n\n" +
          "**Daily habits**: Consistent sleep, 20 min of movement, limit doomscrolling, connect with one person daily.\n\n" +
          "This is general information, not a diagnosis.",
        doctor:
          "Share with a professional: how long you have felt this way, what makes it better or worse, how it affects daily life, family history, and any life changes around when it started.",
        worse:
          "If you are having thoughts of harming yourself, please reach out:\n\n" +
          "- Crisis Text Line: Text HOME to 741741\n" +
          "- 988 Suicide and Crisis Lifeline: Call or text 988\n" +
          "- Emergency: Call 911",
        understand:
          "Based on what you described:\n\n" +
          "**Stress/Burnout**: When you have been running on empty, your body and brain shut down. You might feel exhausted, detached, or irritable. This is not laziness \u2014 it is your nervous system saying 'enough.'\n\n" +
          "**Intrusive thoughts**: These are incredibly common (up to 94% of people have them). The difference is whether you get stuck trying to neutralize them. Try labeling them as 'just a thought' and letting them pass.\n\n" +
          "**Social anxiety**: Discomfort around people often stems from fear of judgment. Small steps help \u2014 start with brief, low-stakes interactions.\n\n" +
          "**What helps right now**:\n" +
          "- 5-4-3-2-1 grounding technique\n" +
          "- Journal intrusive thoughts without judging them\n" +
          "- Consistent sleep schedule\n" +
          "- Limit social media comparison\n\n" +
          "This is general information, not a diagnosis.",
        cope:
          "Here are evidence-based strategies:\n\n" +
          "**For anxiety**: Box breathing (4-4-4-4), progressive muscle relaxation, 5-4-3-2-1 grounding\n\n" +
          "**For intrusive thoughts**: Label them, do not fight them, write them down\n\n" +
          "**For social anxiety**: Start small, prepare conversation topics, remember most people are focused on themselves\n\n" +
          "**Daily**: Consistent sleep, 20 min movement, limit doomscrolling, connect with one person",
      },
      physical: {
        default:
          "Based on your symptoms, here are some things to consider. Many physical symptoms have common causes that are very treatable once identified.\n\n" +
          "Keep track of: when symptoms start, what makes them better or worse, and how severe they are. This info helps your doctor narrow things down.\n\n" +
          "This is general information, not a diagnosis.",
        understand:
          "Physical symptoms can stem from many sources:\n\n" +
          "- **Infections**: viral or bacterial, often with fever\n" +
          "- **Stress**: very physical symptoms (headaches, stomach issues, fatigue)\n" +
          "- **Nutritional**: deficiencies in iron, vitamin D, B12\n" +
          "- **Hormonal**: thyroid, blood sugar\n" +
          "- **Lifestyle**: sleep, exercise, diet\n\n" +
          "A key question: does it happen at rest or with exertion? Constant or intermittent? This helps narrow it down.\n\n" +
          "This is general information, not a diagnosis.",
      },
    };

    function getFallback(text) {
      var lower = text.toLowerCase();
      var ctx = type === 'mental' ? 'mental' : 'physical';
      var responses = fallbacks[ctx] || fallbacks.physical;

      if (/doctor|appointment|professional|therapist/i.test(lower))
        return responses.doctor || responses.default;
      if (/worse|emergency|urgent|danger|harm|hurt.*self|suicide/i.test(lower))
        return responses.worse || responses.default;
      if (/understand|explain|what.*going|what.*is|tell.*more|what.*mean|why/i.test(lower))
        return responses.understand || responses.default;
      if (/cope|coping|deal|manage|strategy|technique|help|what.*do/i.test(lower))
        return responses.cope || responses.default;
      return responses.default;
    }

    // -- DOM helpers --
    function addUserMessage(text) {
      var div = document.createElement('div');
      div.className = 'flex gap-3 justify-end animate-fade-in';

      var bubble = document.createElement('div');
      bubble.className = 'bg-warm-100 rounded-2xl rounded-tr-md p-4 max-w-[85%]';
      var p = document.createElement('p');
      p.className = 'text-sm text-warm-700 leading-relaxed';
      p.textContent = text;
      bubble.appendChild(p);

      var avatar = document.createElement('div');
      if (userAvatarUrl) {
        var img = document.createElement('img');
        img.src = userAvatarUrl;
        img.alt = 'You';
        img.className = 'w-8 h-8 rounded-full object-cover shrink-0';
        avatar.appendChild(img);
      } else {
        avatar.className =
          'w-8 h-8 rounded-full bg-warm-200 flex items-center justify-center shrink-0';
        avatar.innerHTML =
          '<svg class="w-4 h-4 text-warm-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';
      }

      div.appendChild(bubble);
      div.appendChild(avatar);
      chat.appendChild(div);
      div.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    function addAIMessage(text) {
      var div = document.createElement('div');
      div.className = 'flex gap-3 animate-fade-in';

      var avatar = document.createElement('div');
      avatar.className =
        'w-8 h-8 rounded-full bg-gradient-to-br from-' + themeClass + '-300 to-' + themeClass + '-500 flex items-center justify-center shrink-0';
      avatar.innerHTML =
        '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>';

      var bubble = document.createElement('div');
      bubble.className =
        'bg-' + themeClass + '-50 border border-' + themeClass + '-200 rounded-2xl rounded-tl-md p-4 max-w-[85%]';

      var content = document.createElement('div');
      content.className = 'text-sm text-' + themeClass + '-700 leading-relaxed space-y-2';

      var paragraphs = text.split(/\n\n+/);
      for (var i = 0; i < paragraphs.length; i++) {
        var para = document.createElement('p');
        var parts = paragraphs[i].split(/\*\*(.*?)\*\*/g);
        for (var j = 0; j < parts.length; j++) {
          if (j % 2 === 1) {
            var strong = document.createElement('strong');
            strong.textContent = parts[j];
            para.appendChild(strong);
          } else {
            var lines = parts[j].split(/\n/);
            for (var k = 0; k < lines.length; k++) {
              if (k > 0) para.appendChild(document.createElement('br'));
              para.appendChild(document.createTextNode(lines[k]));
            }
          }
        }
        content.appendChild(para);
      }

      bubble.appendChild(content);
      div.appendChild(avatar);
      div.appendChild(bubble);
      chat.appendChild(div);
      div.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    function showTyping() {
      var div = document.createElement('div');
      div.className = 'flex gap-3 animate-fade-in';
      div.id = 'typing-' + type;
      var dotsClass = 'w-2 h-2 rounded-full bg-' + themeClass + '-300 animate-bounce';
      div.innerHTML =
        '<div class="w-8 h-8 rounded-full bg-gradient-to-br from-' + themeClass + '-300 to-' + themeClass + '-500 flex items-center justify-center shrink-0">' +
        '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>' +
        '</div>' +
        '<div class="bg-' + themeClass + '-50 border border-' + themeClass + '-200 rounded-2xl rounded-tl-md px-4 py-3">' +
        '<div class="flex gap-1.5">' +
        '<div class="' + dotsClass + '"></div>' +
        '<div class="' + dotsClass + '" style="animation-delay:0.15s"></div>' +
        '<div class="' + dotsClass + '" style="animation-delay:0.3s"></div>' +
        '</div></div>';
      chat.appendChild(div);
      div.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    function hideTyping() {
      var el = document.getElementById('typing-' + type);
      if (el) el.remove();
    }

    // -- Input handling --
    input.addEventListener('input', function () {
      input.style.height = 'auto';
      input.style.height = Math.min(input.scrollHeight, 96) + 'px';
      sendBtn.disabled = input.value.trim().length === 0;
    });

    function sendMessage() {
      var text = input.value.trim();
      if (!text) return;

      addUserMessage(text);
      history.push({ role: 'user', content: text });
      input.value = '';
      input.style.height = 'auto';
      sendBtn.disabled = true;
      showTyping();

      if (geminiConfigured) {
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        fetch(chatUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            Accept: 'application/json',
          },
          body: JSON.stringify({
            message: text,
            history: history.slice(0, -1),
            context: context,
          }),
        })
          .then(function (res) {
            return res.json();
          })
          .then(function (data) {
            hideTyping();
            if (data.response) {
              history.push({ role: 'assistant', content: data.response });
              addAIMessage(data.response);
            } else {
              var fb = getFallback(text);
              history.push({ role: 'assistant', content: fb });
              addAIMessage(fb);
            }
          })
          .catch(function (err) {
            console.error('AI chat error:', err);
            hideTyping();
            var fb = getFallback(text);
            history.push({ role: 'assistant', content: fb });
            addAIMessage(fb);
          });
      } else {
        setTimeout(function () {
          hideTyping();
          var response = getFallback(text);
          history.push({ role: 'assistant', content: response });
          addAIMessage(response);
        }, 800 + Math.random() * 700);
      }
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });
  });
});
