/* ================================================================
   car3d.js — Modèle 3D Alpine A526 avec Three.js (ES Module)
================================================================ */

import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

let inited=false, scene, camera, renderer, controls, wheels=[], rafId=null, is3D=true;

window.initAlpineCar = function(){
  if(inited){ onResize(); return; }
  const stage=document.getElementById('carStage');
  if(!stage || stage.clientWidth===0) return;
  inited=true;

  const w=stage.clientWidth, h=stage.clientHeight;
  scene=new THREE.Scene();
  camera=new THREE.PerspectiveCamera(38, w/h, 0.1, 200);
  camera.position.set(7, 3.0, 7.5);

  renderer=new THREE.WebGLRenderer({antialias:true, alpha:true});
  renderer.setPixelRatio(Math.min(2, window.devicePixelRatio));
  renderer.setSize(w, h);
  renderer.shadowMap.enabled=true; renderer.shadowMap.type=THREE.PCFSoftShadowMap;
  renderer.toneMapping=THREE.ACESFilmicToneMapping; renderer.toneMappingExposure=1.05;
  stage.insertBefore(renderer.domElement, stage.firstChild);

  // Lights
  scene.add(new THREE.AmbientLight(0xCFE6FF, 0.5));
  const key=new THREE.DirectionalLight(0xffffff, 1.5);
  key.position.set(6,10,6); key.castShadow=true;
  key.shadow.mapSize.set(2048,2048);
  Object.assign(key.shadow.camera,{left:-7,right:7,top:7,bottom:-7});
  scene.add(key);
  const fill=new THREE.DirectionalLight(0xFF5C9D, 0.5); fill.position.set(-6,4,-3); scene.add(fill);
  const rim=new THREE.DirectionalLight(0x1E6FC4, 0.55); rim.position.set(0,4,-8); scene.add(rim);
  const under=new THREE.PointLight(0xFF5C9D,0.4,20); under.position.set(0,-1,0); scene.add(under);

  // Ground shadow
  const ground=new THREE.Mesh(new THREE.CircleGeometry(9,64), new THREE.ShadowMaterial({opacity:0.4}));
  ground.rotation.x=-Math.PI/2; ground.position.y=-0.34; ground.receiveShadow=true; scene.add(ground);

  // Materials
  const matBlue=new THREE.MeshStandardMaterial({color:0x0a4ea0, metalness:0.5, roughness:0.28});
  const matBlueLt=new THREE.MeshStandardMaterial({color:0x1E6FC4, metalness:0.5, roughness:0.3});
  const matPink=new THREE.MeshStandardMaterial({color:0xFF5C9D, metalness:0.4, roughness:0.3, emissive:0x3a0d24, emissiveIntensity:0.25});
  const matDark=new THREE.MeshStandardMaterial({color:0x05121f, metalness:0.6, roughness:0.5});
  const matCarbon=new THREE.MeshStandardMaterial({color:0x14161a, metalness:0.4, roughness:0.6});
  const matTire=new THREE.MeshStandardMaterial({color:0x141414, metalness:0.0, roughness:0.92});
  const matRim=new THREE.MeshStandardMaterial({color:0xC2C7CD, metalness:0.9, roughness:0.22});
  const matHalo=new THREE.MeshStandardMaterial({color:0x1a1a1a, metalness:0.7, roughness:0.35});

  const car=new THREE.Group();

  // ---- MONOCOQUE (lathe-ish via scaled boxes + tapered shapes) ----
  // Main tub
  const tub=new THREE.Mesh(new THREE.BoxGeometry(0.95,0.34,2.6), matBlue);
  tub.position.set(0,0.18,0.1); tub.castShadow=true; car.add(tub);
  // Rounded top of tub
  const tubTop=new THREE.Mesh(new THREE.CylinderGeometry(0.42,0.48,2.6,24,1,false,0,Math.PI), matBlue);
  tubTop.rotation.set(0,0,Math.PI/2); tubTop.rotation.y=Math.PI/2; tubTop.position.set(0,0.34,0.1); tubTop.castShadow=true;
  // orient properly: cylinder axis along Z
  tubTop.rotation.set(Math.PI/2,0,0);
  car.add(tubTop);

  // Nose: tapering cone forward
  const nose=new THREE.Mesh(new THREE.CylinderGeometry(0.07,0.34,2.0,20), matBlue);
  nose.rotation.x=Math.PI/2; nose.position.set(0,0.16,2.35); nose.castShadow=true; car.add(nose);
  const noseTip=new THREE.Mesh(new THREE.SphereGeometry(0.075,16,16), matPink);
  noseTip.position.set(0,0.16,3.32); car.add(noseTip);

  // Front wing (multi-element)
  const fwMain=new THREE.Mesh(new THREE.BoxGeometry(1.9,0.05,0.5), matDark);
  fwMain.position.set(0,0.03,3.05); fwMain.castShadow=true; car.add(fwMain);
  const fwFlap=new THREE.Mesh(new THREE.BoxGeometry(1.7,0.04,0.28), matPink);
  fwFlap.position.set(0,0.15,3.18); fwFlap.rotation.x=-0.18; car.add(fwFlap);
  for(const x of [-0.95,0.95]){
    const ep=new THREE.Mesh(new THREE.BoxGeometry(0.05,0.30,0.5), matBlueLt);
    ep.position.set(x,0.16,3.05); car.add(ep);
  }

  // Sidepods (curved with cylinders) + pink BWT band
  for(const x of [-0.66,0.66]){
    const sp=new THREE.Mesh(new THREE.CylinderGeometry(0.3,0.34,1.9,20), matBlue);
    sp.rotation.x=Math.PI/2; sp.position.set(x,0.2,-0.05); sp.scale.set(1,1,1); sp.castShadow=true; car.add(sp);
    const band=new THREE.Mesh(new THREE.BoxGeometry(0.36,0.12,1.4), matPink);
    band.position.set(x,0.34,-0.05); car.add(band);
    const inlet=new THREE.Mesh(new THREE.CylinderGeometry(0.2,0.2,0.12,16), matDark);
    inlet.rotation.x=Math.PI/2; inlet.position.set(x,0.22,0.92); car.add(inlet);
  }
  // Bargeboard / floor edges
  const floor=new THREE.Mesh(new THREE.BoxGeometry(1.55,0.05,3.4), matCarbon);
  floor.position.set(0,0.0,0.0); floor.castShadow=true; floor.receiveShadow=true; car.add(floor);

  // Cockpit opening
  const cockpit=new THREE.Mesh(new THREE.BoxGeometry(0.5,0.16,0.7), matDark);
  cockpit.position.set(0,0.4,0.62); car.add(cockpit);
  // Driver helmet hint (pink)
  const helmet=new THREE.Mesh(new THREE.SphereGeometry(0.16,16,16), matPink);
  helmet.position.set(0,0.46,0.5); car.add(helmet);

  // Halo
  const halo=new THREE.Mesh(new THREE.TorusGeometry(0.34,0.045,12,24,Math.PI), matHalo);
  halo.rotation.set(Math.PI/2,0,0); halo.position.set(0,0.52,0.68); halo.castShadow=true; car.add(halo);
  const haloPost=new THREE.Mesh(new THREE.CylinderGeometry(0.035,0.035,0.32,10), matHalo);
  haloPost.position.set(0,0.5,1.02); haloPost.rotation.x=Math.PI/2.3; car.add(haloPost);

  // Engine cover + airbox
  const eng=new THREE.Mesh(new THREE.CylinderGeometry(0.26,0.34,1.7,20), matBlue);
  eng.rotation.x=Math.PI/2; eng.position.set(0,0.36,-0.9); eng.castShadow=true; car.add(eng);
  const airbox=new THREE.Mesh(new THREE.CylinderGeometry(0.14,0.2,0.3,14), matDark);
  airbox.position.set(0,0.62,-0.2); car.add(airbox);
  // shark fin (pink)
  const fin=new THREE.Mesh(new THREE.BoxGeometry(0.03,0.24,1.1), matPink);
  fin.position.set(0,0.66,-1.15); car.add(fin);

  // Rear wing (2026 active aero style)
  for(const x of [-0.62,0.62]){
    const ep=new THREE.Mesh(new THREE.BoxGeometry(0.05,0.7,0.5), matBlueLt);
    ep.position.set(x,0.62,-2.15); ep.castShadow=true; car.add(ep);
  }
  const rwTop=new THREE.Mesh(new THREE.BoxGeometry(1.3,0.06,0.42), matPink);
  rwTop.position.set(0,0.95,-2.15); rwTop.castShadow=true; car.add(rwTop);
  const rwBot=new THREE.Mesh(new THREE.BoxGeometry(1.2,0.05,0.34), matDark);
  rwBot.position.set(0,0.5,-2.15); car.add(rwBot);
  const beam=new THREE.Mesh(new THREE.BoxGeometry(1.1,0.04,0.2), matCarbon);
  beam.position.set(0,0.28,-2.2); car.add(beam);

  // Wheels with suspension arms
  const wp=[[-0.98,-0.02,1.55],[0.98,-0.02,1.55],[-1.02,-0.02,-1.5],[1.02,-0.02,-1.5]];
  for(const [x,y,z] of wp){
    const tire=new THREE.Mesh(new THREE.CylinderGeometry(0.42,0.42,0.36,32), matTire);
    tire.rotation.z=Math.PI/2; tire.position.set(x,y,z); tire.castShadow=true; car.add(tire); wheels.push(tire);
    // pink tyre wall ring
    const ring=new THREE.Mesh(new THREE.TorusGeometry(0.42,0.02,8,32), matPink);
    ring.rotation.y=Math.PI/2; ring.position.set(x+(x>0?0.18:-0.18),y,z); car.add(ring);
    const rim=new THREE.Mesh(new THREE.CylinderGeometry(0.24,0.24,0.38,18), matRim);
    rim.rotation.z=Math.PI/2; rim.position.set(x,y,z); car.add(rim);
    const disc=new THREE.Mesh(new THREE.CylinderGeometry(0.17,0.17,0.05,20), matDark);
    disc.rotation.z=Math.PI/2; disc.position.set(x,y,z); car.add(disc);
    // suspension arm to body
    const arm=new THREE.Mesh(new THREE.CylinderGeometry(0.025,0.025,Math.abs(x)-0.3,8), matCarbon);
    arm.rotation.z=Math.PI/2; arm.position.set(x*0.6,y,z); car.add(arm);
  }

  scene.add(car);

  controls=new OrbitControls(camera, renderer.domElement);
  controls.enableDamping=true; controls.dampingFactor=0.08;
  controls.minDistance=5; controls.maxDistance=15;
  controls.maxPolarAngle=Math.PI/2.05;
  controls.target.set(0,0.3,0);
  controls.autoRotate=true; controls.autoRotateSpeed=0.9;
  controls.update();

  // UI
  const rotBtn=$('rotBtn'), stillBtn=$('stillBtn'), view3dBtn=$('view3dBtn');
  rotBtn.addEventListener('click',()=>{controls.autoRotate=!controls.autoRotate;
    rotBtn.classList.toggle('on',controls.autoRotate);rotBtn.textContent=controls.autoRotate?'Rotation auto':'Rotation off';});
  $('resetView').addEventListener('click',()=>{camera.position.set(7,3.0,7.5);controls.target.set(0,0.3,0);controls.update();});

  stillBtn.addEventListener('click',()=>setMode(false));
  view3dBtn.addEventListener('click',()=>setMode(true));

  function loop(){
    rafId=requestAnimationFrame(loop);
    if(controls.autoRotate){for(const wl of wheels) wl.rotation.x+=0.03;}
    controls.update();
    renderer.render(scene,camera);
  }
  loop();

  function onResize(){const W=stage.clientWidth,H=stage.clientHeight;if(!W||!H)return;
    camera.aspect=W/H;camera.updateProjectionMatrix();renderer.setSize(W,H);}
  new ResizeObserver(onResize).observe(stage);
  window.addEventListener('resize',onResize);
  window._carResize=onResize;
};

function setMode(threeD){
  is3D=threeD;
  const stage=document.getElementById('carStage');
  const still=document.getElementById('carStill');
  const canvas=stage.querySelector('canvas');
  document.getElementById('view3dBtn').classList.toggle('on',threeD);
  document.getElementById('stillBtn').classList.toggle('on',!threeD);
  document.getElementById('rotBtn').style.display=threeD?'':'none';
  document.getElementById('resetView').style.display=threeD?'':'none';
  document.getElementById('carModeLabel').textContent=threeD?'3D':'image fixe';
  document.getElementById('carHint').style.display=threeD?'':'none';
  stage.classList.toggle('mode3d',threeD);
  if(canvas) canvas.style.display=threeD?'':'none';
  still.style.display=threeD?'none':'flex';
}
window._carSetMode=setMode;
