!function(n){var r={};function o(e){if(r[e])return r[e].exports;var t=r[e]={i:e,l:!1,exports:{}};return n[e].call(t.exports,t,t.exports,o),t.l=!0,t.exports}o.m=n,o.c=r,o.d=function(e,t,n){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)o.d(n,r,function(e){return t[e]}.bind(null,r));return n},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="/static-dist/",o(o.s=641)}({641:function(e,t){var n=$("#refund-form"),r=n.parents(".modal"),o=n.find("#reasonNote"),a=n.find(".warnning");n.find('[name="reason[type]"]').on("change",function(){var e=$(this).find("option:selected");"other"==e.val()?o.val("").removeClass("hide"):o.addClass("hide").val(e.text()),a.text("")}).change(),o.on("change",function(){var e=$(this);120<e.val().length?a.text(Translator.trans("order.refund.reason_limit_hint")):0==e.val().length?a.text(Translator.trans("order.refund.reason_required_hint")):a.text("")}).change(),n.on("submit",function(){return"reason"==n.find("#reasonType").val()?(a.text(Translator.trans("order.refund.reason_choose_hint")),!1):120<o.val().length?(a.text(Translator.trans("order.refund.reason_limit_hint")),!1):0==o.val().length?(a.text(Translator.trans("order.refund.reason_required_hint")),!1):void r.find("[type=submit]").button("loading").attr("disabled",!0)})}});