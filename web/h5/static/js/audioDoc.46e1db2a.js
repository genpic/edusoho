(window.webpackJsonp=window.webpackJsonp||[]).push([["audioDoc"],{"32e6":function(t,e){function n(t,e){t.onload=function(){this.onerror=this.onload=null,e(null,t)},t.onerror=function(){this.onerror=this.onload=null,e(new Error("Failed to load "+this.src),t)}}function i(t,e){t.onreadystatechange=function(){"complete"!=this.readyState&&"loaded"!=this.readyState||(this.onreadystatechange=null,e(null,t))}}t.exports=function(t,e,r){var o=document.head||document.getElementsByTagName("head")[0],a=document.createElement("script");"function"==typeof e&&(r=e,e={}),e=e||{},r=r||function(){},a.type=e.type||"text/javascript",a.charset=e.charset||"utf8",a.async=!("async"in e)||!!e.async,a.src=t,e.attrs&&function(t,e){for(var n in e)t.setAttribute(n,e[n])}(a,e.attrs),e.text&&(a.text=""+e.text),("onload"in a?n:i)(a,r),a.onload||n(a,r),o.appendChild(a)}},af59:function(t,e,n){"use strict";n.r(e),n("e7e5");var i=n("d399"),r=(n("96cf"),n("3b8d")),o=n("db72"),a=n("32e6"),s=n.n(a),u=n("2f62"),c=(n("3ce7"),{data:function(){return{isEncryptionPlus:!1}},computed:Object(o.a)({},Object(u.d)("course",{sourceType:function(t){return t.sourceType},selectedPlanId:function(t){return t.selectedPlanId},taskId:function(t){return t.taskId},details:function(t){return t.details},joinStatus:function(t){return t.joinStatus},user:function(t){return t.user}})),created:function(){this.initPlayer()},methods:{getParams:function(){return this.joinStatus?{query:{courseId:this.selectedPlanId,taskId:this.taskId}}:{query:{courseId:this.selectedPlanId,taskId:this.taskId},params:{preview:1}}},initPlayer:function(){var t=Object(r.a)(regeneratorRuntime.mark((function t(){var e,n,r=this;return regeneratorRuntime.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(this.$refs.audio&&(this.$refs.audio.innerHTML=""),e=this.$route.query,this.isEncryptionPlus=e.isEncryptionPlus,!e.isEncryptionPlus){t.next=6;break}return Object(i.a)("该浏览器不支持云视频播放，请下载App"),t.abrupt("return");case 6:n={id:"course-detail__audio--content",user:this.user,playlist:e.playlist,template:e.text,autoplay:!0,simpleMode:!0},this.$store.commit("UPDATE_LOADING_STATUS",!0),this.loadPlayerSDK().then((function(t){r.$store.commit("UPDATE_LOADING_STATUS",!1);var e=new t(n);e.on("ready",(function(){})),e.on("datapicker.start",(function(t){})),e.on("ended",(function(){})),e.on("timeupdate",(function(t){}))}));case 9:case"end":return t.stop()}}),t,this)})));return function(){return t.apply(this,arguments)}}(),loadPlayerSDK:function(){if(!window.AudioPlayerSDK){var t="//service-cdn.qiqiuyun.net/js-sdk/audio-player/sdk-v1.js?v="+Date.now()/1e3/60;return new Promise((function(e,n){s()(t,(function(t){t&&n(t),e(window.AudioPlayerSDK)}))}))}return Promise.resolve(window.AudioPlayerSDK)}}}),d=n("a6c2"),l=Object(d.a)(c,(function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"course-detail__audio"},[e("div",{directives:[{name:"show",rawName:"v-show",value:!this.isEncryptionPlus,expression:"!isEncryptionPlus"}],ref:"audio",staticClass:"course-detail__audio--content",attrs:{id:"course-detail__audio--content"}})])}),[],!1,null,null,null);e.default=l.exports}}]);