!function(a,b){"use strict";function c(a,b,c){this.init(a,b,c)}c.prototype={init:function(a,b,c){var d={saveValidator:function(a){return a.canSave()},debug:!1,idleTimeout:20,saveThrottleEnabled:!0,saveThrottleTimeout:10,saveDebounceEnabled:!0,saveDebounceTimeout:2,saveDebounceMaximum:11};this.lastSerialized=null,this.saveThrottleTimer=null,this.saveDebounceTimer=null,this.saveDebounceBegan=null,this.idleTimer=null,this.enabled=!1,this.queuedSave=!1,this.options=d,this.options=_(d).extend(c),this.options.form=a,this.options.saver=b,this.saving=!1,this.status={idle:0,saving:1,busy:2,throttled:3,debounced:4,saveFailed:5,disabled:6},this.cachedForm(this.getFormSerialized())},isEnabled:function(){return this.enabled},enable:function(){this.resetIdleTimer(),this.enabled=!0},disable:function(){this.enabled=!1,this.disableIdleTimer()},disableIdleTimer:function(){a.clearTimeout(this.idleTimer),this.idleTimer=null},requestSave:function(a){return this.resetIdleTimer(),this.enabled?"undefined"!=typeof a&&a?this.requestQueuedSave():this.saving?(this.queuedSave=!0,this.status.busy):this.options.saveValidator(this)?this.options.saveThrottleEnabled&&this.throttleSave()?(this.resetThrottle(),this.status.throttled):this.options.saveDebounceEnabled?this.debounceSave():(this.debug("Handling Save Synchronous"),this.handleSave()):(this.debug("Save Not Needed"),this.status.saveFailed):this.status.disabled},requestQueuedSave:function(){var a=this.requestSave();return a===this.status.throttled&&(this.debug("Queuing Save"),this.queuedSave=!0),a},handleSave:function(){if(!this.enabled)return this.status.disabled;if(this.saving)return this.status.busy;this.saving=!0;var a=this,b=this.cachedForm(this.getFormSerialized()),c=function(){a.saving=!1,a.resetThrottle(),a.resetIdleTimer()};return this.options.saver(this,b,c)?this.status.saving:this.status.saveFailed},resetIdleTimer:function(){var b=this;this.idleTimer&&a.clearTimeout(this.idleTimer),this.idleTimer=a.setTimeout(function(){b.requestSave(),b.resetIdleTimer()},1e3*this.options.idleTimeout)},canSave:function(){return!this.cachedFormEquals(this.getFormSerialized())},debounceSave:function(){if(!this.enabled)return this.status.disabled;this.saveDebounceTimer&&a.clearTimeout(this.saveDebounceTimer),this.saveDebounceBegan||(this.saveDebounceBegan=_.now());var b=_.now()-this.saveDebounceBegan,c=1e3*this.options.saveDebounceMaximum-b,d=Math.max(0,Math.min(1e3*this.options.saveDebounceTimeout,c)),e=this;return this.options.saveDebounceMaximum||(d=this.options.saveDebounceTimeout),this.saveDebounceTimer=a.setTimeout(function(){e.debug("Debouncing Expired, Handling Save"),e.saveDebounceBegan=null,e.handleSave()},d),this.debug("Debouncing Save for "+d+"ms"),this.status.debounced},resetThrottle:function(){this.throttleSave(1e3*this.options.saveThrottleTimeout)},throttleSave:function(b){var c=this,d=null!==this.saveThrottleTimer;return null===this.saveThrottleTimer&&"undefined"!=typeof b&&(this.saveThrottleTimer=a.setTimeout(function(){c.debug("Throttle Expired"),c.saveThrottleTimer=null,c.queuedSave&&(c.debug("Handling Queued Save"),c.handleSave(),c.queuedSave=!1)},b),this.debug("Throttling Save")),d},getForm:function(){return this.options.form},getFormSerialized:function(){return this.getForm().serializeArray()},cachedForm:function(a){return"undefined"!=typeof a&&(this.lastSerialized=a),this.lastSerialized},cachedFormEquals:function(a){return _(this.cachedForm()).isEqual(a)},debug:function(b){this.options.debug&&a.console.log("SaverCoordinator: "+b)}},a.Concrete||(a.Concrete={}),a.Concrete.composer||(a.Concrete.composer={}),a.Concrete.composer.SaveCoodinator=c,b.fn.saveCoordinator=function(a,d){return this.each(function(){var e=b(this),f=new c(e,a,d);e.data("SaveCoordinator",f)})}}(this,jQuery);