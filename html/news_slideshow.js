/*
Script: news_slideshow.js

	Lightweight news_galerie script, based on BarackSlideshow and Fx.MorphList from Guillermo Rauch

	License:
		MIT-style license.

	Authors:
		Oliver Lohoff, contao4you
		
*/

var news_slideshow = new Class({

  Extends: Fx.MorphList,

  options: {/*
    onShow: $empty,*/
    auto: false,
    autostart: false,
    autointerval: 4000,
    transition: 'fade',
    infobox: true,
    tween: { duration: 700 }
  },

  initialize: function(menu, images, loader, infos, options){
    this.infozone = new Fx.Morph($(infos));
    this.infos = $$('#' + infos +  ' li').setStyle('display', 'none');
    this.infoheights = new Array();
    this.infos.each(function(item, index){
      item.setStyle('display', 'list-item');
      this.infoheights[index] = $(infos).getStyle('height').toInt();
      item.setStyle('display', 'none');
    }, this);
    this.infozone.set({'height': 0});

    this.parent(menu, options);
    this.images = $(images);
    this.imagesitems = this.images.getChildren().fade('hide');
    $(loader).fade('in');
    new Asset.images(this.images.getElements('img').map(function(el) { return el.setStyle('display', 'none').get('src'); }), { onComplete: function() {
      this.loaded = true;
      $(loader).fade('out');
      if (this.current) this.show(this.items.indexOf(this.current));
      else if (this.options.auto && this.options.autostart) this.progress();
    }.bind(this) });
  },

  auto: function(){
    if (!this.options.auto) return false;
    clearTimeout(this.autotimer);
    this.autotimer = this.progress.delay(this.options.autointerval, this);
  },

  onClick: function(event, item){
    this.curindex = this.imagesitems.indexOf(this.curimage);
    this.parent(event, item);
    event.stop();
    this.show(this.items.indexOf(item));
    clearTimeout(this.autotimer);
  },

  show: function(index) {
    if (!this.loaded) return;
    var image = this.imagesitems[index];
    if (image == this.curimage) return;
    image.set('tween', this.options.tween).dispose().inject(this.curimage || this.images.getFirst(), this.curimage ? 'after' : 'before').fade('hide');
    image.getElement('img').setStyle('display', 'block');
    var trans = this.options.transition.split('-');
    switch(trans[0]){
      case 'slide':
        var dir = Array.pick(trans[1], 'left');
        var prop = (dir == 'left' || dir == 'right') ? 'left' : 'top';
        image.fade('show').setStyle(prop, image['offset' + (prop == 'left' ? 'Width' : 'Height')] * ((dir == 'bottom' || dir == 'right') ? 1 : -1)).tween(prop, 0);
        break;
      case 'fade': image.fade('in'); break;
    }
    if ($chk(this.curindex) && this.infos[this.curindex]) {
        this.infozone.start({'height': 0});
        this.infos[this.curindex].setStyle('display', 'none');
    }
    image.get('tween').chain(function(){
      this.auto();
      this.infozone.start({'height': this.infoheights[index]});
      this.infos[index].setStyle('display', 'list-item');
      this.fireEvent('show', image);
    }.bind(this));
    this.curimage = image;
    this.setCurrent(this.items[index])
    this.morphTo(this.items[index]);
    return this;
  },

  progress: function(){
    this.curindex = this.imagesitems.indexOf(this.curimage);
    this.show((this.curimage && (this.curindex + 1 < this.imagesitems.length)) ? this.curindex + 1 : 0);
  }

});

/*
 * Get deprecated function back
 * see http://mootools.net/docs/core/Core/Core#Deprecated-Functions:chk
 */
var $chk = function (obj)
{
	return !!(obj || obj === 0);
};
