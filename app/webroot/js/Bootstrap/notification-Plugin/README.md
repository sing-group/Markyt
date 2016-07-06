sticky
======

A jQuery plugin for sticky notification, tip, message, information, warning or something else, super easy to use and have a nicely UI/UX.

## UI
![image](screenshot/tip.png)

### Require
- Development
  ```html
  <link rel="stylesheet" href="src/jquery.sticky.css">
  <script type="text/javascript" src="src/jquery.sticky.js"></script>
  ```

- Production
  ```html
  <link rel="stylesheet" href="dist/jquery.sticky.min.css">
  <script type="text/javascript" src="dist/jquery.sticky.min.js"></script>
  ```

- Via bower

  - Install
    ```
    $ bower install tjatse.jquery.sticky
    ```

  - Update
    ```
    $ bower update tjatse.jquery.sticky
    ```

## Usage
```javascript
$.sticky([options]);
// or
$.fn.sticky([options]);
// Clean up
$.sticky.dequeue([id]);
```

### Options
```javascript
// Icon of notification.
icon           : '',
// Title of notification.
title          : '',
// Body of notification.
body           : '',
// Width of notification holder.
width          : 300,
// Animation speed.
speed          : 500,
// Position of notification, including: top-left, top-mid, top-right, mid-left, mid-mid, mid-right, bottom-left, bottom-mid, bottom-right.
position       : 'top-right',
// Hide tips after the number of milliseconds.
hideAfter      : 3000,
// A value indicates whether display close button or not.
closeable      : true,
// A value indicates whether use https://github.com/daneden/animate.css animations.
useAnimateCss  : false,
// Animations come from https://github.com/daneden/animate.css
// Should be formatted as {'POSITION': ['ENTER_ANIMATION', 'EXIT_ANIMATION']}
// Notes: don't change these if no necessary.
animations     : {
    'top-left'    : ['zoomInRight', 'zoomOutRight'],
    'top-mid'     : ['zoomInUp', 'zoomOutUp'],
    'top-right'   : ['zoomInLeft', 'zoomOutLeft'],
    'mid-left'    : ['zoomInRight', 'zoomOutRight'],
    'mid-mid'     : ['zoomIn', 'zoomOut'],
    'mid-right'   : ['zoomInLeft', 'zoomOutLeft'],
    'bottom-left' : ['zoomInRight', 'zoomOutRight'],
    'bottom-mid'  : ['zoomInDown', 'zoomOutDown'],
    'bottom-right': ['zoomInLeft', 'zoomOutLeft']
},
// Class names of sticky.
iconClassName  : 'sticky-icon',
bodyClassName  : 'sticky-body',
titleClassName : 'sticky-title',
stickyClassName: 'sticky',
holderClassName: 'sticky-holder'
// Events:
// onShown     : function(id){},
// onHidden    : function(id){}
```

## Notes
- All the options are optional except `body`.
- If you wanna get an amazing UX, try to enable animations via `useAnimateCss: true`, and require the [animate.css](https://github.com/daneden/animate.css) in you html like:
  ```html
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css">
  ```

- Feel free to edit the `src/jquery.sticky.css` to match your theme.
- If the `sticky` was fired multi-times, the notifications will be queued to display.

## Clean house
```javascript
$.sticky.dequeue([id]);
```

`id` is an optional argument, if it was provided, the specific notification will be dismissed, otherwise all of notifications will be hidden.

## Depenecies
- jQuery ^1.3
- animate.css ~3.2.0 (if `useAnimateCss` enabled)

## Live demo
[jquery.sticky](http://tjatse.github.io/jquery/sticky)

## Examples
```javascript
// Simple
$.sticky('hi, every body rock!');

// Advantage
$.sticky({
  icon         : 'img/greet.png',
  title        : 'Greeting',
  body         : 'Hello there, I am jquery.sticky \(^o^)/~.',
  position     : 'top-right',
  useAnimateCss: true,
  onShown      : function(id){
    console.log('shown', id);
  },
  onHidden     : function(id){
    console.log('hidden', id);
  }
});
```

A completed usage is in the `index.html`, or take a further look into the `src/jquery.sticky.js` [L40-80](https://github.com/Tjatse/sticky/blob/master/src/jquery.sticky.js#L40-L80).

## License
Copyright 2014 Tjatse

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.