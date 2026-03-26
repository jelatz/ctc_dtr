<template>
  <div class="anim-wrapper">
    <div class="scene">
      <!-- Door on the LEFT -->
      <div class="door-container">
        <div class="entry-sign">ENTRY</div>
        <div class="door-frame">
          <div class="door"></div>
        </div>
      </div>

      <!-- Character walks RIGHT → LEFT (into the door) -->
      <div class="character">
        <div class="head"></div>
        <div class="body"></div>
        <div class="legs">
          <div class="leg left-leg"></div>
          <div class="leg right-leg"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.anim-wrapper {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(to right, #1d4ed8, #1e40af);
  border-radius: inherit;
  overflow: hidden;
}

.scene {
  position: relative;
  width: 220px;
  height: 36px;
}

/* Door on LEFT */
.door-container {
  position: absolute;
  left: 8px;
  bottom: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  z-index: 3;
}

.entry-sign {
  font-size: 7px;
  font-weight: bold;
  color: #fff;
  border: 1px solid #fff;
  padding: 1px 3px;
  margin-bottom: 2px;
  letter-spacing: 1px;
  border-radius: 1px;
}

.door-frame {
  width: 22px;
  height: 26px;
  border: 1.5px solid rgba(255,255,255,0.9);
  border-bottom: none;
  background: rgba(255,255,255,0.12);
  position: relative;
}

/* Door opens (swings right) then closes after person enters */
.door {
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,0.35);
  transform-origin: right;
  transform: perspective(100px) rotateY(70deg);
  animation: openDoor 0.4s 1.2s forwards, closeDoor 0.5s 1.5s forwards;
}

/* Character starts on right, walks left */
.character {
  position: absolute;
  right: 0;
  bottom: 0;
  z-index: 2;
  /* scaleX(-1) flips character to face left */
  transform: scaleX(-1) translateX(0px);
  animation: walkAcross 1.5s linear forwards;
}

.head {
  width: 8px;
  height: 8px;
  background: #fff;
  border-radius: 50%;
  margin: 0 auto 1px;
}

.body {
  width: 6px;
  height: 13px;
  background: #fff;
  border-radius: 2px;
  margin: 0 auto;
}

.legs {
  display: flex;
  justify-content: center;
  gap: 1px;
  margin-top: -1px;
}

.leg {
  width: 3px;
  height: 8px;
  background: #fff;
  border-radius: 1px;
  transform-origin: top;
}

.left-leg  { animation: stride 0.3s infinite alternate; }
.right-leg { animation: stride 0.3s infinite alternate-reverse; }

/* Walk from right edge toward left door */
@keyframes walkAcross {
  0%   { transform: scaleX(-1) translateX(0px);    opacity: 1; }
  85%  { transform: scaleX(-1) translateX(152px);  opacity: 1; }
  95%  { transform: scaleX(-1) translateX(162px);  opacity: 0; }
  100% { transform: scaleX(-1) translateX(162px);  opacity: 0; }
}

@keyframes stride {
  0%   { transform: rotate(-25deg); }
  100% { transform: rotate(25deg); }
}

/* Door swings open as person approaches */
@keyframes openDoor {
  0%   { transform: perspective(100px) rotateY(70deg); }
  100% { transform: perspective(100px) rotateY(0deg); }
}

/* Door closes after person walks in */
@keyframes closeDoor {
  0%   { transform: perspective(100px) rotateY(0deg); }
  100% { transform: perspective(100px) rotateY(70deg); }
}
</style>
