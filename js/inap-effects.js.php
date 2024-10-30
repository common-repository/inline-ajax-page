
/**
*	 Originally and Copyrighted 2007 by:
*	Aaron Dowden http://anthologyoi.com
*	The code is released under a Creative Commons Liscense
*	(Attribution-NonCommercial-ShareAlike 2.0)
*	TERMS: (Removal this section indicates agreement with these Terms.)
*	For Personal (non-distribution) this notice (sans Terms section) must remain.
*	For Distribution this notice must remain and attribution through
*	a publically accessible "followable" link on applicable information/download page is required.
*	No Commercial use without prior approval.
**/

var AOI_eff = function () {

/**
* The following are used consistantly throughout.
* Parameter and variable "i" is the ID of element for currently processing effect.
* Can have multiple "i"s at the same time,  but they do not overlap or interact.
* Variable "e" is the element itself.
**/
	/*
	*  Private "Global" Variables
	*  delay: Default time (in milliseconds) bewteen frames for all effects
	*  _d is an object with an index of i that holds currently active effect information
	*/
	var delay = 100;
	var _d = [];

	/*
	*  Private Function
	*  Returns the element for a given i.
	*  Is NOT related to prototype or jQUERY.
	*/
	var $ = function (id) {
		return document.getElementById(id);
	};

	return {
		/*
		*	This function sets up the effect
		*	It is a per-application function and will change depending on
		*	How the effects are called and proccessed.
		*/
		start: function () {
			var	i = arguments[0];

			if (!_d[i]) {//This stops the effects from being restarted in the middle

				//_d[i]['eff'] = effect; =  arguments[1];

				_d[i] = arguments[1] || [];

				if (!_d[i].queue) {
					_d[i].queue = '';
				}

				if (!_d[i].mode) {
					if ($(i).style.display === 'block') {
						_d[i].mode = 'hide';
					} else {
						_d[i].mode = 'show';
					}
				}
				AOI_eff.setup(i);
			}
		},

		/*
		* Setup is called when a new effect starts.
		* It does not literally "setup" the effect information
		* It just uses it,  and passes it on.
		* Effects always count to 0,  so "step" should get closer to 0 no matter what.
		* We use a negative step for "hiding" because the change in the display gets larger
		* as time goes by (10 - step),  and a positive step for "showing" so the effect gets
		* smaller as time goes by (step).
		* If neither showing or hiding we just use a positive for ease of use.
		* The individual effects will the determine what we are doing. EG flashing or highlighting.
		*/

		setup: function (i) {

			if ($(i)) { /*Stop for invalid ID*/
				if (_d[i].mode === 'hide') {
					_d[i].step = -10;

					AOI_eff.ready(i, 'hide');
				} else if (_d[i].mode === 'show') {

					_d[i].step = 10;
					AOI_eff.ready(i, 'show');
				} else if (_d[i].mode === 'other') {
					_d[i].step = 10;
					AOI_eff.ready(i, 'other');
				}

				if (!_d[i].delay) {
					_d[i].delay = delay; /* If a delay is not specifically set,  set it.*/
				}

				AOI_eff.doit(i);
			} else {
				return false;
			}
		},

		/*
		*	Ready is called just before the effects start.
		*	 e is The syles of the current element we are working with.
		*	It is used to make a "show" effect start were whe want it too
		*	It also is used to "remember" the style defaults,  so the effects don't change them.
		*	If the effect is a "show" it also ensures the element is showing before effects start.
		*	m is the passed mode passed from the setup function (saves a lookup.)
		*/

		ready: function (i, m) {
			var e = $(i).style;
			switch (_d[i].eff) {
				case 'Expand':

					_d[i]['overflow'] = e.overflow;
					_d[i]['lineHeight'] = e.lineHeight;
					_d[i]['letterSpacing'] = e.letterSpacing;

					if (m === 'show') {
						e.overflow = 'hidden';
						e.lineHeight = '300%';
						e.letterSpacing = '1em';
					}

					break;
				case 'SlideUp':
					_d[i]['height'] = $(i).offsetHeight;

					if (m === 'show') { /*We need an object to be displayed to retrieve height.*/
						e.position='absolute'; /*Pull the element out of its default location*/
						e.visibility='hidden'; /*Hide the element*/
						e.display='block';/*"Display" the hidden element*/
						_d[i]['height'] = $(i).offsetHeight;
						e.visibility=''; /*Show it*/
						e.position='relative'; /*Put it back where it was*/
						e.height = '0px'; /*shrink it for the effect.*/
					}

					break;

				case 'ScrollLeft':
					_d[i]['marginLeft'] = e.marginLeft;
					if (m === 'show') {
						e.marginLeft = 80+'%';
					}
					break;
				case 'Fade':
					e.zoom = 1;/*IE fix*/
					if (m === 'show') {
						e.filter = 'alpha(opacity=0)';
						e.opacity = 0;
					}
					break;
			}

			if (m === 'show') {
				e.display = 'block';
			}
		},
		/*
		* Function doit does the actual effects
		* e is The syles of the current element we are working with.
		* m is the mode: 'show',  'hide' or 'other'
		* s is the current step (number between -10 and 10) with 0 being the end of the effect
		* v is just an empty variable for effects to use.
		*/
		doit: function (i) {
			var e = $(i).style; /**/
			var m = _d[i].mode;
			var s = _d[i].step;
			var v = 0;

			if ( _d[i].step !== 0  ) {
				switch (_d[i].eff) {
					case 'Expand':
						if 	( m === 'hide' ) {
							v = (100+ (10+s)*20); /*IE fix*/
							e.lineHeight = v+'%';
							e.letterSpacing = ((10+s)*3)+'px';
							_d[i].step += 1;
						} else {
							v = (300 - (10-s)*20);/*IE fix*/
							e.lineHeight = v+'%';
							e.letterSpacing = s*2+'px';
							_d[i].step -= 1;
						}
					break;

					case 'SlideUp':
						if 	( m === 'hide' ) {
							e.height =  Math.floor( _d[i]['height']*s*-0.1)+'px';
							_d[i].step += 1;
						} else {
							e.height = Math.floor( _d[i]['height']*(10-s)*0.1)+'px';
							_d[i].step -= 1;
						}
					break;

					case 'ScrollLeft':

						if 	( m === 'hide' ) {
							if ((!window.innerHeight && s < -3) || window.innerHeight ) {/*IE fix*/
								e.marginLeft=((10 + s)*10)+'%';
							}
							_d[i].step += 1;
						} else {
							e.marginLeft=(s*8)+'%';
							_d[i].step -= 1;
						}
					break;

					case 'Fade' :
						if 	( m === 'hide' ) {
							e.opacity = (s)/-10;
							e.filter = 'alpha(opacity='+(s*-10)+')';
							_d[i].step+= 1;
						} else {
							e.filter = 'alpha(opacity='+((10-s)*10)+')';
							e.opacity = (10 - s)/10;
							_d[i].step -= 1;
						}
						break;
					default:
						_d[i].step = 0;
						break;

				}
					setTimeout("AOI_eff.doit('"+i+"');",  _d[i].delay); /*Call next frame after delay.*/
			} else {
				AOI_eff.finish(i); /*Clean up*/
			}
		},
		/*
		* Function finish performs any final duties:
		*	hides element when "hiding"
		*	Restores defaults after effects are finished:
		*	eg after shrinking an elelemnt it's height is returned to normal.
		* it will then process the queue and start the next effect if needed.
		* Variable "d2": dummy variable to temporarily hold next effects information (while_d[i] is still in use)
		* variable "var": an array of the values for the next effect.
		* e is The syles of the current element we are working with.
		*/
		finish: function (i) {
			var e = $(i).style; /**/
			if ( _d[i].mode === 'hide' ) {
				e.display = 'none';
			}

			switch (_d[i].eff) {
				case 'Expand':
					if ( _d[i]['overflow'] ) {
						e.overflow = _d[i]['overflow'];
					}

					e.lineHeight ="normal";
					e.letterSpacing ="normal";

					break;
				case 'SlideUp':
					e.height = _d[i]['height']+'px';
					break;
				case 'ScrollLeft':
					e.marginLeft = _d[i]['marginLeft'] ;
					break;
				default :
					e.opacity = 10;
					e.filter = 'alpha(Opacity=100)';
			}

			if ( _d[i].queue.length > 0 ) {/* Checks to see if there is another effect*/
				var val;

				val = _d[i].queue.shift().split('::'); /*Gets values for first queue'd item*/
				if (!val[2]) { /*Sets effect*/
					val[2] = _d[i].eff;
				}
				if (!val[3]) { /*Sets effect*/
					val[3] = _d[i].delay;
				}

				d2 = {'mode': val[0],  'eff': val[2],  'queue': _d[i].queue,  'delay': _d[i].delay}; /*Sets values in dummy variable*/

				_d[i] = null;
				_d[val[1]] = [];
				_d[val[1]] = d2;
				AOI_eff.setup(val[1]);
			} else {
				_d[i] = null;
			}
		}
	};
}();