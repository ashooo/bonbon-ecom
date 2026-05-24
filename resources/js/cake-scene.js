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
    renderer.shadowMap.type = THREE.PCFShadowMap;
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
    // Top tier: lightest shade #f4d2d0
    const chocolateRidgedMat = new THREE.MeshStandardMaterial({
        color: 0xf4d2d0,
        roughness: 0.68,
        metalness: 0.04,
        bumpMap: ridgedTex,
        bumpScale: 0.02,
    });

    // Bottom tier: rich pinkish-brown #b3625e
    const chocolateRidgedMatBottom = new THREE.MeshStandardMaterial({
        color: 0xb3625e,
        roughness: 0.65,
        metalness: 0.04,
        bumpMap: ridgedTex,
        bumpScale: 0.02,
    });

    // Middle tier: medium pinkish-brown #d69a97
    const chocolateCrumbMat = new THREE.MeshStandardMaterial({
        color: 0xd69a97,
        roughness: 0.82,
        metalness: 0.02,
        bumpMap: crumbTex,
        bumpScale: 0.02,
    });

    const chocolateTopMat = new THREE.MeshStandardMaterial({
        color: 0xf4d2d0,
        roughness: 0.55,
        metalness: 0.02,
    });

    const chocolateTopMatLight = new THREE.MeshStandardMaterial({
        color: 0xe2b5b3,
        roughness: 0.52,
        metalness: 0.06,
    });

    // Elegant glossy white icing material
    const icingMat = new THREE.MeshPhysicalMaterial({
        color: 0xffffff,
        roughness: 0.12,
        metalness: 0.02,
        clearcoat: 1.0,
        clearcoatRoughness: 0.08,
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
        color: 0x7e3a3a, // Deepest elegant shade from the palette
        roughness: 0.62,
        metalness: 0.04,
    });

    // --- Helper: Create a piped white frosting dollop ---
    function createFrostingDollop(scale = 1) {
        const group = new THREE.Group();

        // Base swirl of the dollop
        const baseGeo = new THREE.SphereGeometry(0.05 * scale, 8, 8);
        const base = new THREE.Mesh(baseGeo, icingMat);
        base.scale.set(1.2, 0.7, 1.2);
        group.add(base);

        // Peak swirl
        const peakGeo = new THREE.SphereGeometry(0.035 * scale, 8, 8);
        const peak = new THREE.Mesh(peakGeo, icingMat);
        peak.position.y = 0.035 * scale;
        peak.scale.set(0.8, 1.6, 0.8);
        group.add(peak);

        return group;
    }

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

        // --- Glossy Chocolate Icing Top Layer ---
        const icingHeight = 0.045;
        const chocoIcingMat = new THREE.MeshPhysicalMaterial({
            color: 0x3b1a0a, // Rich dark fudge chocolate
            roughness: 0.1,
            metalness: 0.08,
            clearcoat: 1.0,
            clearcoatRoughness: 0.04,
        });
        const icingGeo = new THREE.CylinderGeometry(
            radiusTop * 1.012, radiusTop * 1.012, icingHeight, 64
        );
        const icingMesh = new THREE.Mesh(icingGeo, chocoIcingMat);
        icingMesh.position.y = height / 2 + icingHeight / 2;
        icingMesh.castShadow = true;
        icingMesh.receiveShadow = true;
        group.add(icingMesh);

        // --- Realistic Chocolate Drips Cascading Down ---
        const chocoRimGroup = new THREE.Group();
        // Thin rim ring that connects drips seamlessly to the icing cap
        const rimGeo = new THREE.TorusGeometry(radiusTop * 1.012, 0.016, 8, 64);
        const rimMesh = new THREE.Mesh(rimGeo, chocoIcingMat);
        rimMesh.rotation.x = Math.PI / 2;
        rimMesh.position.y = height / 2 + 0.002;
        rimMesh.castShadow = true;
        chocoRimGroup.add(rimMesh);

        const numDrips = Math.floor(radiusTop * 38);
        // Pre-generate drip pattern with natural randomness
        const dripPattern = [];
        for (let d = 0; d < numDrips; d++) {
            // Use seeded-like wave math for organic variation
            const baseLen = 0.05 + Math.sin(d * 1.7) * 0.04 + Math.cos(d * 0.9) * 0.03;
            // Every 3rd-5th drip is a long dramatic drip
            const isLong = (d % 3 === 0 || d % 7 === 0);
            const extraLen = isLong ? 0.06 + Math.random() * 0.08 : 0;
            dripPattern.push(Math.max(0.02, baseLen + extraLen + Math.random() * 0.025));
        }

        for (let d = 0; d < numDrips; d++) {
            const angle = (d / numDrips) * Math.PI * 2;
            const dripR = radiusTop * 1.012;
            const dripLen = dripPattern[d];

            // Vary thickness — longer drips are slightly thicker
            const thickness = 0.012 + (dripLen > 0.1 ? 0.004 : 0) + Math.random() * 0.002;

            // Main drip body — tapered cylinder (wider at top, thinner at tip)
            const cylGeo = new THREE.CylinderGeometry(thickness * 0.7, thickness, dripLen, 8);
            const cyl = new THREE.Mesh(cylGeo, chocoIcingMat);
            cyl.position.set(
                Math.cos(angle) * dripR,
                height / 2 - dripLen / 2 + 0.005,
                Math.sin(angle) * dripR
            );
            cyl.castShadow = true;
            chocoRimGroup.add(cyl);

            // Rounded droplet bulb at the bottom of each drip
            const bulbSize = thickness * 1.2 + (dripLen > 0.1 ? 0.004 : 0);
            const bulbGeo = new THREE.SphereGeometry(bulbSize, 8, 8);
            const bulb = new THREE.Mesh(bulbGeo, chocoIcingMat);
            bulb.position.set(
                Math.cos(angle) * dripR,
                height / 2 - dripLen + 0.005,
                Math.sin(angle) * dripR
            );
            bulb.scale.set(1.0, 1.35, 1.0); // Elongated teardrop shape
            bulb.castShadow = true;
            chocoRimGroup.add(bulb);
        }
        group.add(chocoRimGroup);

        // --- Scattered Gold & White Pearl Balls in the Drip Zone ---
        const pearlGroup = new THREE.Group();
        const numGoldBalls = Math.floor(radiusTop * 12);
        const numWhiteBalls = Math.floor(radiusTop * 8);

        // Gold pearl balls scattered on the tier side
        for (let i = 0; i < numGoldBalls; i++) {
            const angle = (i / numGoldBalls) * Math.PI * 2 + Math.random() * 0.3;
            const ballR = radiusTop + 0.008;
            const yPos = height / 2 - 0.04 - Math.random() * (height * 0.55);
            const size = 0.018 + Math.random() * 0.012;
            const ball = new THREE.Mesh(
                new THREE.SphereGeometry(size, 8, 8),
                goldMat
            );
            ball.position.set(Math.cos(angle) * ballR, yPos, Math.sin(angle) * ballR);
            ball.castShadow = true;
            pearlGroup.add(ball);
        }

        // White pearl balls scattered on the tier side
        for (let i = 0; i < numWhiteBalls; i++) {
            const angle = ((i + 0.5) / numWhiteBalls) * Math.PI * 2 + Math.random() * 0.4;
            const ballR = radiusTop + 0.006;
            const yPos = height / 2 - 0.06 - Math.random() * (height * 0.45);
            const size = 0.012 + Math.random() * 0.008;
            const ball = new THREE.Mesh(
                new THREE.SphereGeometry(size, 8, 8),
                icingMat
            );
            ball.position.set(Math.cos(angle) * ballR, yPos, Math.sin(angle) * ballR);
            ball.castShadow = true;
            pearlGroup.add(ball);
        }
        group.add(pearlGroup);

        // --- Elegant Piped Frosting Dollops Around the Edge ---
        const dollopCount = Math.floor(radiusTop * 20);
        const dollopGroup = new THREE.Group();
        for (let i = 0; i < dollopCount; i++) {
            const angle = (i / dollopCount) * Math.PI * 2;
            const r = radiusTop * 0.94;
            const dollop = createFrostingDollop(1.0);
            dollop.position.set(
                Math.cos(angle) * r,
                height / 2 + icingHeight,
                Math.sin(angle) * r
            );
            dollop.rotation.y = -angle;
            dollop.castShadow = true;
            dollopGroup.add(dollop);
        }
        group.add(dollopGroup);

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
    // --- Build Cake Layers ---
    // Bottom layer - large, ridged chocolate (scaled down to fit perfectly on the stand plate)
    const bottomLayer = createCakeTier(1.1, 1.1, 0.7, chocolateRidgedMatBottom, chocolateTopMat);
    bottomLayer.position.set(0, -10, 0);

    // Middle layer - crumbled/nutty texture (proportionally scaled down)
    const middleLayer = createCakeTier(0.82, 0.82, 0.6, chocolateCrumbMat, chocolateTopMatLight);
    middleLayer.position.set(0, -10, 0);

    // Top layer - ridged chocolate, smaller (proportionally scaled down)
    const topLayer = createCakeTier(0.56, 0.56, 0.5, chocolateRidgedMat, chocolateTopMat);
    topLayer.position.set(0, -10, 0);

    // --- Ruffled Pink Cream Borders at the base of each tier ---
    const ruffleMat = new THREE.MeshStandardMaterial({
        color: 0xf2b5bd, // Soft premium baby pink frosting
        roughness: 0.3,
        metalness: 0.05,
    });

    // Bottom tier base ruffles
    const bottomRuffleGroup = new THREE.Group();
    const bottomTierRadius = 1.1;
    const bottomTierHeight = 0.7;
    for (let i = 0; i < 60; i++) {
        const angle = (i / 60) * Math.PI * 2;
        const r = bottomTierRadius + 0.015;
        const ruffleGeo = new THREE.SphereGeometry(0.048, 8, 8);
        const ruffle = new THREE.Mesh(ruffleGeo, ruffleMat);
        ruffle.position.set(Math.cos(angle) * r, -bottomTierHeight / 2, Math.sin(angle) * r);
        ruffle.scale.set(1.5, 0.6, 1.0);
        ruffle.rotation.y = -angle + Math.PI / 2;
        bottomRuffleGroup.add(ruffle);
    }
    bottomLayer.add(bottomRuffleGroup);

    // Middle tier base ruffles
    const middleRuffleGroup = new THREE.Group();
    const middleTierRadius = 0.82;
    const middleHeight = 0.6;
    for (let i = 0; i < 48; i++) {
        const angle = (i / 48) * Math.PI * 2;
        const r = middleTierRadius + 0.015;
        const ruffleGeo = new THREE.SphereGeometry(0.038, 8, 8);
        const ruffle = new THREE.Mesh(ruffleGeo, ruffleMat);
        ruffle.position.set(Math.cos(angle) * r, -middleHeight / 2, Math.sin(angle) * r);
        ruffle.scale.set(1.5, 0.6, 1.0);
        ruffle.rotation.y = -angle + Math.PI / 2;
        middleRuffleGroup.add(ruffle);
    }
    middleLayer.add(middleRuffleGroup);

    // Top tier base ruffles
    const topRuffleGroup = new THREE.Group();
    const topTierRadius = 0.56;
    const topTierHeight = 0.5;
    for (let i = 0; i < 36; i++) {
        const angle = (i / 36) * Math.PI * 2;
        const r = topTierRadius + 0.012;
        const ruffleGeo = new THREE.SphereGeometry(0.028, 8, 8);
        const ruffle = new THREE.Mesh(ruffleGeo, ruffleMat);
        ruffle.position.set(Math.cos(angle) * r, -topTierHeight / 2, Math.sin(angle) * r);
        ruffle.scale.set(1.5, 0.6, 1.0);
        ruffle.rotation.y = -angle + Math.PI / 2;
        topRuffleGroup.add(ruffle);
    }
    topLayer.add(topRuffleGroup);


    // --- Side Designs for Each Tier ---

    // 1. Bottom Layer: 24 vertical white icing columns + beautiful garland swags
    const bottomSideDesign = new THREE.Group();
    for (let i = 0; i < 24; i++) {
        const angle = (i / 24) * Math.PI * 2;
        const r = bottomTierRadius + 0.004; // Just outside the side wall

        // Vertical column
        const ribGeo = new THREE.CylinderGeometry(0.015, 0.015, bottomTierHeight * 0.94, 8);
        const rib = new THREE.Mesh(ribGeo, icingMat);
        rib.position.set(
            Math.cos(angle) * r,
            0,
            Math.sin(angle) * r
        );
        rib.rotation.y = -angle;
        rib.castShadow = true;
        bottomSideDesign.add(rib);

        // Hanging garland loop between this column and the next
        for (let step = 0; step <= 8; step++) {
            const t = step / 8;
            const swagAngle = ((i + t) / 24) * Math.PI * 2;
            const swagR = bottomTierRadius + 0.012;
            // Hanging curve Y offset
            const y = (bottomTierHeight * 0.36) - Math.sin(t * Math.PI) * 0.09;
            const pearlGeo = new THREE.SphereGeometry(0.013, 6, 6);
            const pearl = new THREE.Mesh(pearlGeo, icingMat);
            pearl.position.set(Math.cos(swagAngle) * swagR, y, Math.sin(swagAngle) * swagR);
            bottomSideDesign.add(pearl);
        }

        // Gold junction bead at the top of each vertical column
        const junctionR = bottomTierRadius + 0.018;
        const goldBeadGeo = new THREE.SphereGeometry(0.024, 6, 6);
        const goldBead = new THREE.Mesh(goldBeadGeo, goldMat);
        goldBead.position.set(Math.cos(angle) * junctionR, bottomTierHeight * 0.38, Math.sin(angle) * junctionR);
        bottomSideDesign.add(goldBead);
    }
    bottomLayer.add(bottomSideDesign);

    // 2. Middle Layer: Cascading Chocolate Drips on the right side, Diamond Quilted Lattice on the left side
    const middleSideDesign = new THREE.Group();
    const dripMat = new THREE.MeshPhysicalMaterial({
        color: 0x421b0b, // Rich dark fudge chocolate color
        roughness: 0.15,
        metalness: 0.05,
        clearcoat: 1.0,
        clearcoatRoughness: 0.05
    });
    const latticeMat = new THREE.MeshStandardMaterial({
        color: 0xfcb0b9, // Soft baby pink lattice diagonal lines
        roughness: 0.5,
        metalness: 0.0
    });

    const dripSteps = 48;
    for (let i = 0; i < dripSteps; i++) {
        const angle = (i / dripSteps) * Math.PI * 2;

        // Right side receives the chocolate glaze drips
        if (angle >= -0.2 && angle <= Math.PI + 0.3) {
            const dripLength = 0.12 + Math.sin(angle * 6.0) * 0.09 + Math.cos(angle * 2.0) * 0.04 + Math.random() * 0.03;
            const dripRadius = middleTierRadius + 0.01;

            const cylinderGeo = new THREE.CylinderGeometry(0.015, 0.015, dripLength, 8);
            const cylinder = new THREE.Mesh(cylinderGeo, dripMat);
            cylinder.position.set(
                Math.cos(angle) * dripRadius,
                middleHeight / 2 - dripLength / 2,
                Math.sin(angle) * dripRadius
            );
            cylinder.castShadow = true;
            middleSideDesign.add(cylinder);

            const bulbGeo = new THREE.SphereGeometry(0.018, 8, 8);
            const bulb = new THREE.Mesh(bulbGeo, dripMat);
            bulb.position.set(
                Math.cos(angle) * dripRadius,
                middleHeight / 2 - dripLength,
                Math.sin(angle) * dripRadius
            );
            bulb.scale.set(1.0, 1.2, 1.0);
            bulb.castShadow = true;
            middleSideDesign.add(bulb);
        }
    }

    // Left side receives the beautiful quilted lattice tufting pattern
    const latticeRows = 5;
    const latticeCols = 16;
    const startAngle = Math.PI + 0.2;
    const endAngle = Math.PI * 2 - 0.2;
    for (let r = 0; r < latticeRows; r++) {
        const y = -middleHeight / 2 + 0.08 + (r / (latticeRows - 1)) * (middleHeight * 0.74);
        for (let c = 0; c <= latticeCols; c++) {
            const angle = startAngle + (c / latticeCols) * (endAngle - startAngle);
            const pearlR = middleTierRadius + 0.005;

            // Tufting bead (alternates colors for high-end look)
            const beadGeo = new THREE.SphereGeometry(0.016, 6, 6);
            const beadMat = (c + r) % 2 === 0 ? goldMat : icingMat;
            const bead = new THREE.Mesh(beadGeo, beadMat);
            bead.position.set(Math.cos(angle) * pearlR, y, Math.sin(angle) * pearlR);
            bead.castShadow = true;
            middleSideDesign.add(bead);

            // Diagonal criss-cross lines
            if (r < latticeRows - 1 && c < latticeCols) {
                const nextY = -middleHeight / 2 + 0.08 + ((r + 1) / (latticeRows - 1)) * (middleHeight * 0.74);
                const nextAngleRight = startAngle + ((c + 1) / latticeCols) * (endAngle - startAngle);

                const p1 = new THREE.Vector3(Math.cos(angle) * (middleTierRadius + 0.003), y, Math.sin(angle) * (middleTierRadius + 0.003));
                const p2 = new THREE.Vector3(Math.cos(nextAngleRight) * (middleTierRadius + 0.003), nextY, Math.sin(nextAngleRight) * (middleTierRadius + 0.003));

                const distance = p1.distanceTo(p2);
                const lineGeo = new THREE.CylinderGeometry(0.0035, 0.0035, distance, 4);
                const line = new THREE.Mesh(lineGeo, latticeMat);
                line.position.copy(p1).add(p2).multiplyScalar(0.5);
                line.quaternion.setFromUnitVectors(new THREE.Vector3(0, 1, 0), p2.clone().sub(p1).normalize());
                middleSideDesign.add(line);
            }
            if (r < latticeRows - 1 && c > 0) {
                const nextY = -middleHeight / 2 + 0.08 + ((r + 1) / (latticeRows - 1)) * (middleHeight * 0.74);
                const nextAngleLeft = startAngle + ((c - 1) / latticeCols) * (endAngle - startAngle);

                const p1 = new THREE.Vector3(Math.cos(angle) * (middleTierRadius + 0.003), y, Math.sin(angle) * (middleTierRadius + 0.003));
                const p2 = new THREE.Vector3(Math.cos(nextAngleLeft) * (middleTierRadius + 0.003), nextY, Math.sin(nextAngleLeft) * (middleTierRadius + 0.003));

                const distance = p1.distanceTo(p2);
                const lineGeo = new THREE.CylinderGeometry(0.0035, 0.0035, distance, 4);
                const line = new THREE.Mesh(lineGeo, latticeMat);
                line.position.copy(p1).add(p2).multiplyScalar(0.5);
                line.quaternion.setFromUnitVectors(new THREE.Vector3(0, 1, 0), p2.clone().sub(p1).normalize());
                middleSideDesign.add(line);
            }
        }
    }
    middleLayer.add(middleSideDesign);

    // 3. Top Layer: Baroque Scroll Embroidery
    const topSideDesign = new THREE.Group();
    const scrollArches = 12;
    for (let i = 0; i < scrollArches; i++) {
        const baseAngle = (i / scrollArches) * Math.PI * 2;

        // Scrolling arch curves
        for (let step = 0; step <= 12; step++) {
            const t = step / 12;
            const angle = baseAngle + (t - 0.5) * (Math.PI * 2 / scrollArches);
            const r = topTierRadius + 0.008;
            const y = -0.08 + Math.sin(t * Math.PI) * 0.12;
            const dotGeo = new THREE.SphereGeometry(0.011, 6, 6);
            const dot = new THREE.Mesh(dotGeo, icingMat);
            dot.position.set(Math.cos(angle) * r, y, Math.sin(angle) * r);
            topSideDesign.add(dot);
        }

        // Baroque junction scroll and gold diamonds
        const jointAngle = baseAngle - (Math.PI / scrollArches);
        const jointR = topTierRadius + 0.012;

        const goldStudGeo = new THREE.SphereGeometry(0.016, 4, 4);
        const goldStud = new THREE.Mesh(goldStudGeo, goldMat);
        goldStud.position.set(Math.cos(jointAngle) * jointR, 0.06, Math.sin(jointAngle) * jointR);
        topSideDesign.add(goldStud);

        const pearlDangleGeo = new THREE.SphereGeometry(0.01, 6, 6);
        const pearlDangle = new THREE.Mesh(pearlDangleGeo, icingMat);
        pearlDangle.position.set(Math.cos(jointAngle) * jointR, 0.02, Math.sin(jointAngle) * jointR);
        topSideDesign.add(pearlDangle);
    }
    topLayer.add(topSideDesign);


    // --- Decorations for top layer: Vibrant 3D Sprinkles ---
    const sprinklesGroup = new THREE.Group();
    const sprinkleColors = [
        0xff6b8b, // Soft rose pink
        0xffa885, // Pastel peach
        0xffea79, // Sweet lemon yellow
        0x70a1ff, // Sky blue
        0x2ed573, // Mint green
        0xff7f50, // Warm coral
        0xffffff  // Crisp white
    ];
    const sprinkleGeo = new THREE.CylinderGeometry(0.012, 0.012, 0.07, 8);

    const icingHeight = 0.045;
    const surfaceY = topTierHeight / 2 + icingHeight; // Sit exactly on top of the icing surface

    for (let i = 0; i < 90; i++) {
        const sprinkleMat = new THREE.MeshStandardMaterial({
            color: sprinkleColors[Math.floor(Math.random() * sprinkleColors.length)],
            roughness: 0.25,
            metalness: 0.02,
        });
        const sprinkle = new THREE.Mesh(sprinkleGeo, sprinkleMat);

        // Circular scattering with wide central clearing for top toppings
        const angle = Math.random() * Math.PI * 2;
        const r = (0.32 + Math.random() * 0.68) * topTierRadius * 0.82;

        sprinkle.rotation.x = Math.PI / 2 + (Math.random() - 0.5) * 0.15;
        sprinkle.rotation.y = Math.random() * Math.PI * 2;
        sprinkle.rotation.z = (Math.random() - 0.5) * 0.15;

        sprinkle.position.set(
            Math.cos(angle) * r,
            surfaceY + 0.005,
            Math.sin(angle) * r
        );
        sprinkle.castShadow = true;
        sprinkle.receiveShadow = true;
        sprinklesGroup.add(sprinkle);
    }
    topLayer.add(sprinklesGroup);


    // --- Cherry Topper in the center ---
    const cherryGroup = new THREE.Group();
    const cherryMat = new THREE.MeshPhysicalMaterial({
        color: 0x8b0000,
        roughness: 0.08,
        metalness: 0.05,
        clearcoat: 1.0,
        clearcoatRoughness: 0.05,
        emissive: 0x3d0000,
        emissiveIntensity: 0.1
    });
    const cherryGeo = new THREE.SphereGeometry(0.09, 20, 20);
    const cherryMesh = new THREE.Mesh(cherryGeo, cherryMat);
    cherryMesh.scale.set(1.0, 0.95, 1.0);
    cherryMesh.castShadow = true;
    cherryMesh.receiveShadow = true;
    cherryGroup.add(cherryMesh);

    // Tall curved organic stem
    const stemPoints = [
        new THREE.Vector3(0, 0.08, 0),
        new THREE.Vector3(0.015, 0.20, 0.01),
        new THREE.Vector3(0.05, 0.32, 0.025),
        new THREE.Vector3(0.09, 0.38, 0.035)
    ];
    const stemCurve = new THREE.CatmullRomCurve3(stemPoints);
    const stemGeo = new THREE.TubeGeometry(stemCurve, 10, 0.006, 8, false);
    const stemMat = new THREE.MeshStandardMaterial({
        color: 0x42541c,
        roughness: 0.72,
        metalness: 0.0
    });
    const stemMesh = new THREE.Mesh(stemGeo, stemMat);
    stemMesh.castShadow = true;
    cherryGroup.add(stemMesh);
    cherryGroup.position.set(0, surfaceY + 0.085, 0);
    topLayer.add(cherryGroup);


    // --- Top Surface Luxury Macarons ---
    function createMacaron(colorHex) {
        const group = new THREE.Group();
        const macaronMat = new THREE.MeshStandardMaterial({
            color: colorHex,
            roughness: 0.5,
            metalness: 0.02,
        });
        const fillingMat = new THREE.MeshStandardMaterial({
            color: 0xffffff,
            roughness: 0.6,
        });

        const shellGeo = new THREE.SphereGeometry(0.08, 16, 12);
        const topShell = new THREE.Mesh(shellGeo, macaronMat);
        topShell.scale.set(1.15, 0.44, 1.15);
        topShell.position.y = 0.022;
        topShell.castShadow = true;
        group.add(topShell);

        const bottomShell = new THREE.Mesh(shellGeo, macaronMat);
        bottomShell.scale.set(1.15, 0.44, 1.15);
        bottomShell.position.y = -0.022;
        bottomShell.castShadow = true;
        group.add(bottomShell);

        const fillingGeo = new THREE.CylinderGeometry(0.082, 0.082, 0.016, 16);
        const filling = new THREE.Mesh(fillingGeo, fillingMat);
        filling.position.y = 0;
        filling.castShadow = true;
        group.add(filling);

        return group;
    }

    // Macaron 1 (soft pastel pink)
    const macaron1 = createMacaron(0xfcb0b9);
    macaron1.position.set(-0.19, surfaceY + 0.025, 0.16);
    macaron1.rotation.set(0.3, 0.5, 0.4);
    macaron1.scale.set(0.9, 0.9, 0.9);
    topLayer.add(macaron1);

    // Macaron 2 (cream pastel)
    const macaron2 = createMacaron(0xf7e1d7);
    macaron2.position.set(0.2, surfaceY + 0.025, -0.16);
    macaron2.rotation.set(-0.3, -0.6, -0.2);
    macaron2.scale.set(0.9, 0.9, 0.9);
    topLayer.add(macaron2);

    // Macaron 3 (dusty rose pink)
    const macaron3 = createMacaron(0xd98880);
    macaron3.position.set(0.12, surfaceY + 0.022, 0.18);
    macaron3.rotation.set(0.25, 0.8, -0.35);
    macaron3.scale.set(0.85, 0.85, 0.85);
    topLayer.add(macaron3);


    // --- Baby's Breath White Floral Sprigs ---
    const floralGroup = new THREE.Group();
    for (let s = 0; s < 3; s++) {
        const sprig = new THREE.Group();
        const wireMat = new THREE.MeshStandardMaterial({ color: 0xc5a880, roughness: 0.5 });
        const stem = new THREE.Mesh(new THREE.CylinderGeometry(0.002, 0.002, 0.22, 4), wireMat);
        stem.rotation.x = Math.PI / 2;
        sprig.add(stem);

        const buds = 5;
        for (let b = 0; b < buds; b++) {
            const budMat = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.7 });
            const bud = new THREE.Mesh(new THREE.SphereGeometry(0.008, 6, 6), budMat);
            bud.position.set(
                (Math.random() - 0.5) * 0.05,
                0.1 + Math.random() * 0.05,
                (Math.random() - 0.5) * 0.05
            );
            sprig.add(bud);
        }
        const sprigAngle = (s / 3) * Math.PI * 2;
        sprig.position.set(Math.cos(sprigAngle) * 0.25, surfaceY + 0.06, Math.sin(sprigAngle) * 0.25);
        sprig.rotation.set((Math.random() - 0.5) * 0.5, sprigAngle, 0.4 + Math.random() * 0.4);
        floralGroup.add(sprig);
    }
    topLayer.add(floralGroup);


    // --- Two Beautiful Premium Chocolate Roses on top surface ---
    const rose1 = createChocolateRose(0.9);
    rose1.position.set(-0.25, surfaceY + 0.025, -0.15);
    rose1.rotation.set(0.1, -0.4, 0.25);
    topLayer.add(rose1);

    const rose2 = createChocolateRose(0.85);
    rose2.position.set(0.24, surfaceY + 0.02, 0.12);
    rose2.rotation.set(-0.15, 0.8, -0.3);
    topLayer.add(rose2);


    // --- Glossy Gold Berries on Top Surface ---
    const goldBerry1 = new THREE.Mesh(new THREE.SphereGeometry(0.035, 8, 8), goldMat);
    goldBerry1.position.set(-0.3, surfaceY + 0.03, 0.05);
    goldBerry1.castShadow = true;
    topLayer.add(goldBerry1);

    const goldBerry2 = new THREE.Mesh(new THREE.SphereGeometry(0.03, 8, 8), goldMat);
    goldBerry2.position.set(0.26, surfaceY + 0.03, -0.06);
    goldBerry2.castShadow = true;
    topLayer.add(goldBerry2);


    // Gold flowers on middle layer (re-positioned to align with scaled-down radius)
    const goldFlower1 = createGoldFlower(1.1);
    goldFlower1.position.set(0.79, 0.15, 0.25);
    goldFlower1.rotation.z = -0.3;
    goldFlower1.rotation.y = -0.5;
    middleLayer.add(goldFlower1);

    const goldFlower2 = createGoldFlower(0.9);
    goldFlower2.position.set(-0.53, -0.2, 0.64);
    goldFlower2.rotation.z = 0.2;
    goldFlower2.rotation.y = 1.2;
    middleLayer.add(goldFlower2);

    // Gold flower on bottom layer (re-positioned to align with scaled-down radius)
    const goldFlower3 = createGoldFlower(1.0);
    goldFlower3.position.set(1.03, 0.1, 0.34);
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

    // ══════════════════════════════════════
    // mainCakeGroup wraps the entire hero cake
    // so we can move/scale it as one unit
    // ══════════════════════════════════════
    const mainCakeGroup = new THREE.Group();
    mainCakeGroup.add(standGroup);
    mainCakeGroup.add(bottomLayer);
    mainCakeGroup.add(middleLayer);
    mainCakeGroup.add(topLayer);
    scene.add(mainCakeGroup);

    // --- Shadow plane (world-level, not in group) ---
    const shadowPlaneGeo = new THREE.PlaneGeometry(30, 20);
    const shadowPlaneMat = new THREE.ShadowMaterial({ opacity: 0.18 });
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

    // Track spawned gallery cakes for animation loop
    const galleryCakes = [];

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
    const cupcakeSprinkleColors = [0xff4444, 0x44cc44, 0x4488ff, 0xffcc00, 0xff88cc, 0xff8844, 0x8844ff];
    for (let i = 0; i < 30; i++) {
        const angle = Math.random() * Math.PI * 2;
        const layer = Math.random();
        const r = 0.06 + layer * 0.14;
        const y = 0.18 + layer * 0.36;
        const sprinkleGeo = new THREE.SphereGeometry(0.012, 4, 4);
        const sprinkleMat = new THREE.MeshStandardMaterial({
            color: cupcakeSprinkleColors[Math.floor(Math.random() * cupcakeSprinkleColors.length)],
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
        bottom: { y: 0.91 },
        middle: { y: 1.605 },
        top: { y: 2.2 },
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
        cameraLookX: 0,
        cameraLookY: 1.4,
        mainCakeX: 0,
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

        // Hero cake group position (gallery slide)
        mainCakeGroup.position.x = state.mainCakeX;

        // Visibility control
        topLayer.visible = state.topOpacity > 0.01;
        middleLayer.visible = state.middleOpacity > 0.01;
        bottomLayer.visible = state.bottomOpacity > 0.01;

        // Gallery cakes — gentle spin
        for (const gc of galleryCakes) {
            // Disabled continuous rotation to improve performance for large catalogs
            // if (gc.visible) gc.rotation.y += 0.004;
        }

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
        camera.lookAt(state.cameraLookX, state.cameraLookY, 0);

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

    // ════════════════════════════════════════════════════
    // createGalleryCake — spawns a colored duplicate cake
    // posX, posZ: 3D world position
    // scale: uniform scale (default 0.55)
    // ════════════════════════════════════════════════════
    function createGalleryCake(productName, posX, posZ, scale = 0.55) {
        const name = (productName || '').toLowerCase();

        // Color palette by flavor keyword
        let tierColors, icingColor, plateColor;
        if (name.includes('chocolate')) {
            tierColors = [0x3d1a0a, 0x5c2a10, 0x7a3518];
            icingColor = 0x2a1008;
            plateColor = 0x1a0a04;
        } else if (name.includes('strawberry')) {
            tierColors = [0xf2a8b0, 0xe8808a, 0xd45060];
            icingColor = 0xb03048;
            plateColor = 0x8a2038;
        } else if (name.includes('ube') || name.includes('purple')) {
            tierColors = [0xc9a8e8, 0xa07ac8, 0x7040a8];
            icingColor = 0x502888;
            plateColor = 0x3a1870;
        } else if (name.includes('matcha') || name.includes('green')) {
            tierColors = [0xc8e0b0, 0x90c070, 0x5a9040];
            icingColor = 0x3a7020;
            plateColor = 0x285010;
        } else if (name.includes('mango') || name.includes('yellow')) {
            tierColors = [0xffe090, 0xffc840, 0xf0a000];
            icingColor = 0xd08000;
            plateColor = 0xa06000;
        } else if (name.includes('red velvet') || name.includes('red')) {
            tierColors = [0xcc2020, 0xa01010, 0x780808];
            icingColor = 0x600008;
            plateColor = 0x400004;
        } else {
            // Vanilla / default cream
            tierColors = [0xf5e8d0, 0xe8d0a8, 0xd0b880];
            icingColor = 0xc0a060;
            plateColor = 0x907040;
        }

        // Clone the hero cake to preserve all intricate details (crumbs, drips, roses)
        const group = mainCakeGroup.clone();

        // The hero cake's layers start hidden/separated at Y=-10 for the scrollytelling.
        // We must fully assemble the clone based on `assembledPositions`.
        if (group.children.length >= 4) {
            // group.children[0] = standGroup
            group.children[1].position.y = assembledPositions.bottom.y;
            group.children[1].visible = true;
            group.children[2].position.y = assembledPositions.middle.y;
            group.children[2].visible = true;
            group.children[3].position.y = assembledPositions.top.y;
            group.children[3].visible = true;
        }

        // Traverse the clone and recolor materials by detecting original hex colors
        const colorMap = new Map([
            [0xf4d2d0, tierColors[0]], // top tier base & cap
            [0xe2b5b3, tierColors[0]], // top tier light
            [0xd69a97, tierColors[1]], // middle tier
            [0xb3625e, tierColors[2]], // bottom tier
            [0x3b1a0a, icingColor],    // drips and icing top
            [0x7e3a3a, plateColor]     // roses
        ]);

        const materialCache = new Map(); // Cache cloned materials to avoid duplicating identical materials

        group.traverse((child) => {
            if (child.isMesh && child.material) {
                const processMat = (mat) => {
                    const hex = mat.color.getHex();
                    if (colorMap.has(hex)) {
                        if (!materialCache.has(mat.uuid)) {
                            const newMat = mat.clone();
                            newMat.color.setHex(colorMap.get(hex));
                            materialCache.set(mat.uuid, newMat);
                        }
                        return materialCache.get(mat.uuid);
                    }
                    return mat;
                };

                if (Array.isArray(child.material)) {
                    child.material = child.material.map(processMat);
                } else {
                    child.material = processMat(child.material);
                }
            }
        });

        group.position.set(posX, 0, posZ);
        group.scale.setScalar(scale);
        group.visible = false;
        scene.add(group);
        galleryCakes.push(group);

        return group;
    }

    return {
        state,
        camera,
        scene,
        assembledPositions,
        mainCakeGroup,
        galleryCakes,
        createGalleryCake,
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
