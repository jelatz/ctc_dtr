<template>
  <div class="anim-wrapper">
    <div class="scene">
      <!-- Door on the RIGHT -->
      <div class="door-container">
        <div class="exit-sign">EXIT</div>
        <div class="door-frame">
          <div class="door"></div>
        </div>
      </div>

      <!-- Character walks LEFT → RIGHT -->
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
  background: linear-gradient(to right, #dc2626, #b91c1c);
  border-radius: inherit;
  overflow: hidden;
}

.scene {
  position: relative;
  width: 220px;
  height: 34px;
}

/* Door on RIGHT */
.door-container {
  position: absolute;
  right: 8px;
  bottom: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  z-index: 3;
}

.exit-sign {
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
  height: 24px;
  border: 1.5px solid rgba(255,255,255,0.9);
  border-bottom: none;
  background: rgba(255,255,255,0.12);
  position: relative;
}

.door {
  width: 100%;
  height: 100%;
  background: rgba(255,255,255,0.35);
  transform-origin: left;
  transform: perspective(100px) rotateY(-70deg);
  animation: closeDoor 0.5s 1.5s forwards;
}

/* Character */
.character {
  position: absolute;
  left: 0;
  bottom: 0;
  z-index: 2;
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
  height: 11px;
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

@keyframes walkAcross {
  0%   { transform: translateX(8px);   opacity: 1; }
  85%  { transform: translateX(152px); opacity: 1; }
  95%  { transform: translateX(162px); opacity: 0; }
  100% { transform: translateX(162px); opacity: 0; }
}

@keyframes stride {
  0%   { transform: rotate(-25deg); }
  100% { transform: rotate(25deg); }
}

@keyframes closeDoor {
  0%   { transform: perspective(100px) rotateY(-70deg); }
  100% { transform: perspective(100px) rotateY(0deg); }
}
</style>