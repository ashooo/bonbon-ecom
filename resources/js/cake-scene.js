import * as THREE from 'three';

/**
 * Creates a realistic 3D chocolate cake scene with three layers,
 * chocolate roses on top, and golden accents.
 */
export function createCakeScene(containerEl) {
    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(
        32,
        containerEl.clientWidth / containerEl.clientHeight,
        0.1,
        100
    );
    camera.position.set(0, 3.2, 8.5);
    camera.lookAt(0, 1.4, 0);

    const renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true,
        powerPreference: 'high-performance',
    });
    renderer.setSize(containerEl.clientWidth, containerEl.clientHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.3;
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    containerEl.appendChild(renderer.domElement);

    // --- Lighting ---
    const ambientLight = new THREE.AmbientLight(0xffe8d6, 0.6);
    scene.add(ambientLight);

    const mainLight = new THREE.DirectionalLight(0xfff0e0, 2.2);
    mainLight.position.set(4, 8, 6);
    mainLight.castShadow = true;
    mainLight.shadow.mapSize.set(1024, 1024);
    mainLight.shadow.camera.near = 0.5;
    mainLight.shadow.camera.far = 20;
    mainLight.shadow.camera.left = -4;
    mainLight.shadow.camera.right = 4;
    mainLight.shadow.camera.top = 6;
    mainLight.shadow.camera.bottom = -2;
    mainLight.shadow.bias = -0.002;
    scene.add(mainLight);

    const fillLight = new THREE.DirectionalLight(0xd4a574, 0.9);
    fillLight.position.set(-4, 5, -2);
    scene.add(fillLight);

    const rimLight = new THREE.DirectionalLight(0xffd4a8, 0.6);
    rimLight.position.set(0, 3, -6);
    scene.add(rimLight);

    const spotLight = new THREE.SpotLight(0xffe4c4, 1.2, 15, Math.PI / 5, 0.4);
    spotLight.position.set(2, 7, 5);
    spotLight.target.position.set(0, 1.5, 0);
    scene.add(spotLight);
    scene.add(spotLight.target);

    // Extra warm front-fill for the chocolate to really glow
    const frontFill = new THREE.PointLight(0xf5c28a, 0.6, 12);
    frontFill.position.set(0, 2.5, 6);
    scene.add(frontFill);

    // --- Texture Loader ---
    const textureLoader = new THREE.TextureLoader();

    const loadTexture = (path, repeatX = 1, repeatY = 1) => {
        const tex = textureLoader.load(path);
        tex.wrapS = THREE.RepeatWrapping;
        tex.wrapT = THREE.RepeatWrapping;
        tex.repeat.set(repeatX, repeatY);
        tex.colorSpace = THREE.SRGBColorSpace;
        return tex;
    };

    const ridgedTex = loadTexture('/images/cake-textures/chocolate_ridged.png', 3, 1);
    const crumbTex = loadTexture('/images/cake-textures/chocolate_crumb.png', 2, 1);

    // --- Materials ---
    const chocolateRidgedMat = new THREE.MeshStandardMaterial({
        map: ridgedTex,
        color: 0x6b3a2a,
        roughness: 0.68,
        metalness: 0.04,
        bumpMap: ridgedTex,
        bumpScale: 0.05,
    });

    const chocolateRidgedMatBottom = new THREE.MeshStandardMaterial({
        map: ridgedTex,
        color: 0x5e3325,
        roughness: 0.65,
        metalness: 0.04,
        bumpMap: ridgedTex,
        bumpScale: 0.06,
    });

    const chocolateCrumbMat = new THREE.MeshStandardMaterial({
        map: crumbTex,
        color: 0x704030,
        roughness: 0.82,
        metalness: 0.02,
        bumpMap: crumbTex,
        bumpScale: 0.08,
    });

    const chocolateTopMat = new THREE.MeshStandardMaterial({
        color: 0x3a1c15,
        roughness: 0.55,
        metalness: 0.08,
    });

    const chocolateTopMatLight = new THREE.MeshStandardMaterial({
        color: 0x4a2518,
        roughness: 0.52,
        metalness: 0.06,
    });

    const goldMat = new THREE.MeshStandardMaterial({
        color: 0xc9a84c,
        roughness: 0.3,
        metalness: 0.8,
        emissive: 0x3a2a00,
        emissiveIntensity: 0.15,
    });

    const goldFlowerMat = new THREE.MeshStandardMaterial({
        color: 0xb8952e,
        roughness: 0.35,
        metalness: 0.75,
        emissive: 0x2a1a00,
        emissiveIntensity: 0.1,
    });

    const standBaseMat = new THREE.MeshStandardMaterial({
        color: 0x2a2a2a,
        roughness: 0.2,
        metalness: 0.9,
    });

    const silverMat = new THREE.MeshStandardMaterial({
        color: 0xcccccc,
        roughness: 0.15,
        metalness: 0.95,
        envMapIntensity: 1.2,
    });

    const roseMat = new THREE.MeshStandardMaterial({
        color: 0x4a2520,
        roughness: 0.62,
        metalness: 0.04,
    });

    // --- Helper: Create a closed cake tier (open cylinder + separate caps) ---
    function createCakeTier(radiusTop, radiusBottom, height, sideMat, topMat) {
        const group = new THREE.Group();

        // Open cylinder for the side
        const sideGeo = new THREE.CylinderGeometry(
            radiusTop, radiusBottom, height, 64, 1, true
        );
        const sideMesh = new THREE.Mesh(sideGeo, sideMat);
        sideMesh.castShadow = true;
        sideMesh.receiveShadow = true;
        group.add(sideMesh);

        // Top cap
        const topGeo = new THREE.CircleGeometry(radiusTop, 64);
        const topMesh = new THREE.Mesh(topGeo, topMat);
        topMesh.rotation.x = -Math.PI / 2;
        topMesh.position.y = height / 2;
        topMesh.receiveShadow = true;
        group.add(topMesh);

        // Bottom cap
        const botGeo = new THREE.CircleGeometry(radiusBottom, 64);
        const botMesh = new THREE.Mesh(botGeo, topMat);
        botMesh.rotation.x = Math.PI / 2;
        botMesh.position.y = -height / 2;
        group.add(botMesh);

        return group;
    }

    // --- Helper: Create chocolate rose ---
    function createChocolateRose(scale = 1) {
        const group = new THREE.Group();
        const petalCount = 10;

        // Outer petals
        for (let i = 0; i < petalCount; i++) {
            const angle = (i / petalCount) * Math.PI * 2;
            const radius = 0.14 * scale;
            const petalGeo = new THREE.SphereGeometry(
                0.09 * scale, 8, 6,
                0, Math.PI * 2,
                0, Math.PI * 0.55
            );
            const petal = new THREE.Mesh(petalGeo, roseMat);
            petal.position.set(
                Math.cos(angle) * radius,
                0.02 * scale * Math.sin(i * 0.7),
                Math.sin(angle) * radius
            );
            petal.rotation.set(
                -0.35 + Math.random() * 0.3,
                angle + Math.random() * 0.3,
                0.15 + Math.random() * 0.25
            );
            petal.scale.set(1.1, 0.55, 1.4);
            petal.castShadow = true;
            group.add(petal);
        }

        // Inner petals
        for (let i = 0; i < 5; i++) {
            const angle = (i / 5) * Math.PI * 2 + 0.3;
            const petalGeo = new THREE.SphereGeometry(0.06 * scale, 6, 5);
            const petal = new THREE.Mesh(petalGeo, roseMat);
            petal.position.set(
                Math.cos(angle) * 0.06 * scale,
                0.05 * scale,
                Math.sin(angle) * 0.06 * scale
            );
            petal.rotation.set(-0.5, angle, 0.4);
            petal.scale.set(1, 0.5, 1.2);
            group.add(petal);
        }

        // Center bud
        const budGeo = new THREE.SphereGeometry(0.05 * scale, 8, 8);
        const bud = new THREE.Mesh(budGeo, roseMat);
        bud.position.y = 0.06 * scale;
        bud.scale.set(1, 1.4, 1);
        group.add(bud);

        return group;
    }

    // --- Helper: Create gold flower accent ---
    function createGoldFlower(scale = 1) {
        const group = new THREE.Group();

        for (let i = 0; i < 6; i++) {
            const angle = (i / 6) * Math.PI * 2;
            const petalGeo = new THREE.SphereGeometry(0.04 * scale, 6, 6);
            const petal = new THREE.Mesh(petalGeo, goldFlowerMat);
            petal.position.set(
                Math.cos(angle) * 0.065 * scale,
                0,
                Math.sin(angle) * 0.065 * scale
            );
            petal.scale.set(1.5, 0.5, 1.5);
            group.add(petal);
        }

        const centerGeo = new THREE.SphereGeometry(0.03 * scale, 6, 6);
        const center = new THREE.Mesh(centerGeo, goldMat);
        center.position.y = 0.01;
        group.add(center);

        return group;
    }

    // --- Build Cake Layers ---
    // Bottom layer - large, ridged chocolate
    const bottomLayer = createCakeTier(1.3, 1.3, 0.95, chocolateRidgedMatBottom, chocolateTopMat);
    bottomLayer.position.set(0, -10, 0);

    // Middle layer - crumbled/nutty texture
    const middleLayer = createCakeTier(1.0, 1.0, 0.85, chocolateCrumbMat, chocolateTopMatLight);
    middleLayer.position.set(0, -10, 0);

    // Top layer - ridged chocolate, smaller
    const topLayer = createCakeTier(0.72, 0.72, 0.78, chocolateRidgedMat, chocolateTopMat);
    topLayer.position.set(0, -10, 0);

    // --- Decorations for top layer ---
    const rosesGroup = new THREE.Group();

    const rose1 = createChocolateRose(1.4);
    rose1.position.set(-0.18, 0.42, -0.12);
    rose1.rotation.y = 0.5;
    rosesGroup.add(rose1);

    const rose2 = createChocolateRose(1.2);
    rose2.position.set(0.22, 0.44, 0.08);
    rose2.rotation.y = -0.8;
    rosesGroup.add(rose2);

    const rose3 = createChocolateRose(1.1);
    rose3.position.set(0.02, 0.40, 0.24);
    rose3.rotation.y = 1.5;
    rosesGroup.add(rose3);

    const rose4 = createChocolateRose(0.9);
    rose4.position.set(-0.25, 0.38, 0.18);
    rose4.rotation.y = 2.3;
    rosesGroup.add(rose4);

    topLayer.add(rosesGroup);

    // Gold flowers on middle layer
    const goldFlower1 = createGoldFlower(1.1);
    goldFlower1.position.set(0.96, 0.15, 0.3);
    goldFlower1.rotation.z = -0.3;
    goldFlower1.rotation.y = -0.5;
    middleLayer.add(goldFlower1);

    const goldFlower2 = createGoldFlower(0.9);
    goldFlower2.position.set(-0.65, -0.2, 0.78);
    goldFlower2.rotation.z = 0.2;
    goldFlower2.rotation.y = 1.2;
    middleLayer.add(goldFlower2);

    // Gold flower on bottom layer
    const goldFlower3 = createGoldFlower(1.0);
    goldFlower3.position.set(1.22, 0.1, 0.4);
    goldFlower3.rotation.z = -0.2;
    goldFlower3.rotation.y = -0.6;
    bottomLayer.add(goldFlower3);

    // --- Cake Stand ---
    const standGroup = new THREE.Group();

    // Stand base plate (dark)
    const basePlateGeo = new THREE.CylinderGeometry(1.55, 1.55, 0.08, 64);
    const basePlate = new THREE.Mesh(basePlateGeo, standBaseMat);
    basePlate.position.y = 0;
    basePlate.receiveShadow = true;
    standGroup.add(basePlate);

    // Bevel ring
    const bevelGeo = new THREE.TorusGeometry(1.55, 0.025, 8, 64);
    const bevel = new THREE.Mesh(bevelGeo, standBaseMat);
    bevel.rotation.x = Math.PI / 2;
    bevel.position.y = 0.04;
    standGroup.add(bevel);

    // Silver cone pedestal
    const pedestalGeo = new THREE.CylinderGeometry(0.15, 0.45, 1.3, 32);
    const pedestal = new THREE.Mesh(pedestalGeo, silverMat);
    pedestal.position.y = -0.69;
    pedestal.castShadow = true;
    standGroup.add(pedestal);

    // Gold vine on pedestal
    const vineGroup = new THREE.Group();
    for (let i = 0; i < 8; i++) {
        const t = i / 7;
        const y = -1.3 + t * 1.3;
        const angle = t * Math.PI * 2;
        const r = 0.18 + t * 0.18;
        const vineSegGeo = new THREE.SphereGeometry(0.022, 6, 6);
        const vineSeg = new THREE.Mesh(vineSegGeo, goldMat);
        vineSeg.position.set(
            Math.cos(angle) * r,
            y,
            Math.sin(angle) * r
        );
        vineSeg.scale.set(1.4, 0.6, 1.4);
        vineGroup.add(vineSeg);
    }

    const vineFlower1 = createGoldFlower(0.75);
    vineFlower1.position.set(0.28, -0.9, 0.18);
    vineGroup.add(vineFlower1);

    const vineFlower2 = createGoldFlower(0.65);
    vineFlower2.position.set(-0.22, -0.35, 0.22);
    vineFlower2.rotation.z = 0.3;
    vineGroup.add(vineFlower2);

    standGroup.add(vineGroup);

    // Stand foot base
    const footGeo = new THREE.CylinderGeometry(0.55, 0.6, 0.1, 32);
    const foot = new THREE.Mesh(footGeo, goldMat);
    foot.position.y = -1.38;
    foot.receiveShadow = true;
    standGroup.add(foot);

    standGroup.position.y = 0.52;
    scene.add(standGroup);

    // --- Shadow plane ---
    const shadowPlaneGeo = new THREE.PlaneGeometry(12, 12);
    const shadowPlaneMat = new THREE.ShadowMaterial({ opacity: 0.2 });
    const shadowPlane = new THREE.Mesh(shadowPlaneGeo, shadowPlaneMat);
    shadowPlane.rotation.x = -Math.PI / 2;
    shadowPlane.position.y = -0.9;
    shadowPlane.receiveShadow = true;
    scene.add(shadowPlane);

    // --- Floating golden particles ---
    const particleCount = 50;
    const particleGeo = new THREE.BufferGeometry();
    const particlePositions = new Float32Array(particleCount * 3);
    for (let i = 0; i < particleCount; i++) {
        particlePositions[i * 3] = (Math.random() - 0.5) * 12;
        particlePositions[i * 3 + 1] = Math.random() * 7 - 1;
        particlePositions[i * 3 + 2] = (Math.random() - 0.5) * 10;
    }
    particleGeo.setAttribute('position', new THREE.BufferAttribute(particlePositions, 3));
    const particleMat = new THREE.PointsMaterial({
        color: 0xd4a574,
        size: 0.025,
        transparent: true,
        opacity: 0.25,
        sizeAttenuation: true,
    });
    const particles = new THREE.Points(particleGeo, particleMat);
    scene.add(particles);

    // --- Add layers to scene ---
    scene.add(bottomLayer);
    scene.add(middleLayer);
    scene.add(topLayer);

    // ═══════════════════════════════════════
    // CUPCAKE (right side, spinning)
    // ═══════════════════════════════════════
    const cupcakeGroup = new THREE.Group();

    // Cupcake liner (fluted)
    const linerMat = new THREE.MeshStandardMaterial({
        color: 0xf0ebe5,
        roughness: 0.6,
        metalness: 0.0,
        side: THREE.DoubleSide,
    });
    const linerGeo = new THREE.CylinderGeometry(0.22, 0.16, 0.22, 16, 1, true);
    const linerMesh = new THREE.Mesh(linerGeo, linerMat);
    linerMesh.castShadow = true;
    cupcakeGroup.add(linerMesh);

    // Liner ridges (decorative)
    for (let i = 0; i < 16; i++) {
        const angle = (i / 16) * Math.PI * 2;
        const ridgeGeo = new THREE.BoxGeometry(0.005, 0.22, 0.02);
        const ridge = new THREE.Mesh(ridgeGeo, linerMat);
        const r = 0.19;
        ridge.position.set(Math.cos(angle) * r, 0, Math.sin(angle) * r);
        ridge.rotation.y = angle;
        cupcakeGroup.add(ridge);
    }

    // Liner bottom cap
    const linerBottomGeo = new THREE.CircleGeometry(0.16, 16);
    const linerBottom = new THREE.Mesh(linerBottomGeo, linerMat);
    linerBottom.rotation.x = Math.PI / 2;
    linerBottom.position.y = -0.11;
    cupcakeGroup.add(linerBottom);

    // Cake body (golden brown muffin top)
    const cakeMat = new THREE.MeshStandardMaterial({
        color: 0xd4a050,
        roughness: 0.75,
        metalness: 0.0,
    });
    const cakeBodyGeo = new THREE.SphereGeometry(0.22, 16, 10, 0, Math.PI * 2, 0, Math.PI * 0.55);
    const cakeBody = new THREE.Mesh(cakeBodyGeo, cakeMat);
    cakeBody.position.y = 0.06;
    cakeBody.castShadow = true;
    cupcakeGroup.add(cakeBody);

    // Frosting swirl (cream colored, layered spheres)
    const frostingMat = new THREE.MeshStandardMaterial({
        color: 0xfff8ee,
        roughness: 0.35,
        metalness: 0.02,
    });
    const frostingGroup = new THREE.Group();
    // Main swirl layers
    for (let layer = 0; layer < 5; layer++) {
        const t = layer / 4;
        const r = 0.18 - t * 0.12;
        const y = 0.18 + t * 0.10;
        const count = Math.max(3, 8 - layer * 1);
        for (let i = 0; i < count; i++) {
            const angle = (i / count) * Math.PI * 2 + layer * 0.4;
            const blobGeo = new THREE.SphereGeometry(0.06 - t * 0.015, 8, 6);
            const blob = new THREE.Mesh(blobGeo, frostingMat);
            blob.position.set(
                Math.cos(angle) * r,
                y,
                Math.sin(angle) * r
            );
            blob.scale.set(1.2, 0.7, 1.2);
            frostingGroup.add(blob);
        }
    }
    // Tip
    const tipGeo = new THREE.SphereGeometry(0.04, 8, 8);
    const tip = new THREE.Mesh(tipGeo, frostingMat);
    tip.position.y = 0.58;
    tip.scale.set(1, 1.5, 1);
    frostingGroup.add(tip);
    // cupcakeGroup.add(frostingGroup);

    // Colorful sprinkles on frosting
    const sprinkleColors = [0xff4444, 0x44cc44, 0x4488ff, 0xffcc00, 0xff88cc, 0xff8844, 0x8844ff];
    for (let i = 0; i < 30; i++) {
        const angle = Math.random() * Math.PI * 2;
        const layer = Math.random();
        const r = 0.06 + layer * 0.14;
        const y = 0.18 + layer * 0.36;
        const sprinkleGeo = new THREE.SphereGeometry(0.012, 4, 4);
        const sprinkleMat = new THREE.MeshStandardMaterial({
            color: sprinkleColors[Math.floor(Math.random() * sprinkleColors.length)],
            roughness: 0.4,
            metalness: 0.1,
        });
        const sprinkle = new THREE.Mesh(sprinkleGeo, sprinkleMat);
        sprinkle.position.set(
            Math.cos(angle) * r,
            y + (Math.random() - 0.5) * 0.04,
            Math.sin(angle) * r
        );
        // cupcakeGroup.add(sprinkle);
    }

    cupcakeGroup.scale.set(0.6, 0.6, 0.6);
    cupcakeGroup.position.set(3.5, 3.0, -1);
    cupcakeGroup.visible = false;
    // scene.add(cupcakeGroup);

    // ═══════════════════════════════════════
    // SIGNBOARD (left side, static)
    // ═══════════════════════════════════════
    const signboardGroup = new THREE.Group();

    const woodMat = new THREE.MeshStandardMaterial({
        color: 0xb5884a,
        roughness: 0.75,
        metalness: 0.0,
    });

    const chalkMat = new THREE.MeshStandardMaterial({
        color: 0x2a3a2a,
        roughness: 0.9,
        metalness: 0.0,
    });

    // Left leg
    const legGeo = new THREE.BoxGeometry(0.04, 0.9, 0.04);
    const leftLeg = new THREE.Mesh(legGeo, woodMat);
    leftLeg.position.set(-0.22, -0.1, 0);
    leftLeg.rotation.z = -0.12;
    // signboardGroup.add(leftLeg);

    // Right leg
    const rightLeg = new THREE.Mesh(legGeo, woodMat);
    rightLeg.position.set(0.22, -0.1, 0);
    rightLeg.rotation.z = 0.12;
    // signboardGroup.add(rightLeg);

    // Back support legs
    const backLegGeo = new THREE.BoxGeometry(0.03, 0.75, 0.03);
    const backLeftLeg = new THREE.Mesh(backLegGeo, woodMat);
    backLeftLeg.position.set(-0.18, -0.12, -0.15);
    backLeftLeg.rotation.x = 0.2;
    backLeftLeg.rotation.z = -0.08;
    // signboardGroup.add(backLeftLeg);

    const backRightLeg = new THREE.Mesh(backLegGeo, woodMat);
    backRightLeg.position.set(0.18, -0.12, -0.15);
    backRightLeg.rotation.x = 0.2;
    backRightLeg.rotation.z = 0.08;
    // signboardGroup.add(backRightLeg);

    // Board frame (wooden border)
    const frameGeo = new THREE.BoxGeometry(0.52, 0.62, 0.03);
    const frame = new THREE.Mesh(frameGeo, woodMat);
    frame.position.set(0, 0.28, 0);
    frame.castShadow = true;
    // signboardGroup.add(frame);

    // Chalkboard face
    const boardGeo = new THREE.BoxGeometry(0.46, 0.56, 0.005);
    const board = new THREE.Mesh(boardGeo, chalkMat);
    board.position.set(0, 0.28, 0.018);
    // signboardGroup.add(board);

    // "BonBon" text on chalkboard using canvas texture
    const signCanvas = document.createElement('canvas');
    signCanvas.width = 256;
    signCanvas.height = 256;
    const signCtx = signCanvas.getContext('2d');
    // Dark green chalkboard background
    signCtx.fillStyle = '#2a3a2a';
    signCtx.fillRect(0, 0, 256, 256);
    // Chalk-like text
    signCtx.fillStyle = '#f5ebe0';
    signCtx.font = 'italic 42px serif';
    signCtx.textAlign = 'center';
    signCtx.fillText('BonBons', 128, 80);
    signCtx.font = '26px serif';
    signCtx.fillText('PH', 128, 115);
    // Decorative line
    signCtx.strokeStyle = '#c9a84c';
    signCtx.lineWidth = 2;
    signCtx.beginPath();
    signCtx.moveTo(60, 130);
    signCtx.lineTo(196, 130);
    signCtx.stroke();
    // Sub-text
    signCtx.fillStyle = '#d4c4a8';
    signCtx.font = '18px sans-serif';
    signCtx.fillText('✦ Recommendations ✦', 128, 165);
    signCtx.font = '14px sans-serif';
    signCtx.fillStyle = '#a89880';
    signCtx.fillText('Our best picks', 128, 195);
    signCtx.fillText('just for you', 128, 215);

    const signTexture = new THREE.CanvasTexture(signCanvas);
    signTexture.colorSpace = THREE.SRGBColorSpace;
    const signFaceMat = new THREE.MeshStandardMaterial({
        map: signTexture,
        roughness: 0.85,
        metalness: 0.0,
    });
    const signFaceGeo = new THREE.PlaneGeometry(0.44, 0.54);
    const signFace = new THREE.Mesh(signFaceGeo, signFaceMat);
    signFace.position.set(0, 0.28, 0.022);
    signboardGroup.add(signFace);

    signboardGroup.scale.set(0.55, 0.55, 0.55);
    signboardGroup.position.set(-3.5, 3.0, -1);
    signboardGroup.rotation.y = 0.3;
    signboardGroup.visible = false;
    // scene.add(signboardGroup);

    // --- State ---
    let rotationSpeed = 0.003;
    let autoRotation = 0;
    let animationFrameId = null;

    const assembledPositions = {
        bottom: { y: 0.56 },
        middle: { y: 1.48 },
        top: { y: 2.32 },
    };

    const state = {
        topY: -10,
        topOpacity: 0,
        middleY: -10,
        middleOpacity: 0,
        bottomY: -10,
        bottomOpacity: 0,
        cameraY: 3.2,
        cameraZ: 8.5,
        cupcakeVisible: false,
        cupcakeX: 3.5,
        cupcakeY: 3.0,
        signboardVisible: false,
        signboardX: -3.5,
        signboardY: 3.0,
    };

    // --- Animation Loop ---
    function animate() {
        animationFrameId = requestAnimationFrame(animate);

        autoRotation += rotationSpeed;

        topLayer.rotation.y = autoRotation;
        middleLayer.rotation.y = autoRotation;
        bottomLayer.rotation.y = autoRotation;
        standGroup.rotation.y = autoRotation * 0.25;

        topLayer.position.y = state.topY;
        middleLayer.position.y = state.middleY;
        bottomLayer.position.y = state.bottomY;

        // Visibility control
        topLayer.visible = state.topOpacity > 0.01;
        middleLayer.visible = state.middleOpacity > 0.01;
        bottomLayer.visible = state.bottomOpacity > 0.01;

        // Cupcake (spinning independently)
        cupcakeGroup.visible = state.cupcakeVisible;
        cupcakeGroup.position.x = state.cupcakeX;
        cupcakeGroup.position.y = state.cupcakeY;
        if (state.cupcakeVisible) {
            cupcakeGroup.rotation.y = autoRotation * 2;
        }

        // Signboard (static, no rotation)
        signboardGroup.visible = state.signboardVisible;
        signboardGroup.position.x = state.signboardX;
        signboardGroup.position.y = state.signboardY;

        // Particles float
        const positions = particleGeo.attributes.position.array;
        for (let i = 0; i < particleCount; i++) {
            positions[i * 3 + 1] += 0.002;
            if (positions[i * 3 + 1] > 7) positions[i * 3 + 1] = -1;
            positions[i * 3] += Math.sin(Date.now() * 0.0008 + i) * 0.0008;
        }
        particleGeo.attributes.position.needsUpdate = true;

        camera.position.y = state.cameraY;
        camera.position.z = state.cameraZ;
        camera.lookAt(0, 1.4, 0);

        renderer.render(scene, camera);
    }

    animate();

    // --- Resize ---
    function onResize() {
        camera.aspect = containerEl.clientWidth / containerEl.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(containerEl.clientWidth, containerEl.clientHeight);
    }
    window.addEventListener('resize', onResize);

    return {
        state,
        assembledPositions,
        setRotationSpeed(speed) {
            rotationSpeed = speed;
        },
        dispose() {
            if (animationFrameId) cancelAnimationFrame(animationFrameId);
            window.removeEventListener('resize', onResize);
            renderer.dispose();
            containerEl.removeChild(renderer.domElement);
        },
    };
}
