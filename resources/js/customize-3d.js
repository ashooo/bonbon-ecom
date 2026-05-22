import * as THREE from "three";
import { GLTFLoader } from "three/examples/jsm/loaders/GLTFLoader.js";
import { gsap } from "gsap";

const gltfLoader = new GLTFLoader();
const blenderCakeCache = new Map();
const toppingLayoutCache = new Map();
let oozingInsideAssetPromise = null;

const loadBlenderCakeAsset = (shape, layers) => {
    const slug = String(shape || "Round").toLowerCase();
    const tierCount = Math.max(1, Math.min(4, Number(layers || 1)));
    const url = `/models/bonbon-cakes/${slug}_${tierCount}_tier.glb`;
    if (!blenderCakeCache.has(url)) {
        blenderCakeCache.set(
            url,
            new Promise((resolve, reject) => {
                gltfLoader.load(url, resolve, undefined, reject);
            }),
        );
    }

    return blenderCakeCache.get(url);
};

const loadOozingInsideAsset = () => {
    if (!oozingInsideAssetPromise) {
        oozingInsideAssetPromise = new Promise((resolve, reject) => {
            gltfLoader.load(
                "/models/bonbon-cakes/oozing_inside.glb",
                resolve,
                undefined,
                reject,
            );
        });
    }

    return oozingInsideAssetPromise;
};

const loadToppingLayoutAsset = (shape, layers) => {
    const slug = String(shape || "Round").toLowerCase();
    const tierCount = Math.max(1, Math.min(4, Number(layers || 1)));
    const url = `/models/bonbon-cakes/toppings/toppings_${slug}_${tierCount}_tier.glb`;
    if (!toppingLayoutCache.has(url)) {
        toppingLayoutCache.set(
            url,
            new Promise((resolve, reject) => {
                gltfLoader.load(url, resolve, undefined, reject);
            }),
        );
    }

    return toppingLayoutCache.get(url);
};

const hexToNumber = (hex, fallback) => {
    if (typeof hex !== "string" || !/^#[0-9a-f]{6}$/i.test(hex)) {
        return fallback;
    }

    return Number.parseInt(hex.slice(1), 16);
};

const darkenHex = (hex, factor = 0.74) => {
    if (typeof hex !== "string" || !/^#[0-9a-f]{6}$/i.test(hex)) {
        return "#c7a49d";
    }

    const r = Math.round(Number.parseInt(hex.slice(1, 3), 16) * factor);
    const g = Math.round(Number.parseInt(hex.slice(3, 5), 16) * factor);
    const b = Math.round(Number.parseInt(hex.slice(5, 7), 16) * factor);

    return `#${r.toString(16).padStart(2, "0")}${g.toString(16).padStart(2, "0")}${b.toString(16).padStart(2, "0")}`;
};

const getCakeSizeScale = (size) => {
    const inches = Number(size || 6);
    const scaleMap = {
        6: 1,
        8: 1.13,
        10: 1.27,
        12: 1.42,
    };

    return scaleMap[inches] || 1;
};

const getShapeVisualScale = (shape) => {
    const scaleMap = {
        Round: 1,
        Square: 0.88,
        Heart: 0.9,
    };

    return scaleMap[shape] || 1;
};

const getTopTierVisibleFactor = (layers) => {
    const tierCount = Math.max(1, Math.min(4, Number(layers || 1)));
    const factorMap = {
        1: 0.78,
        2: 0.62,
        3: 0.5,
        4: 0.4,
    };

    return factorMap[tierCount] || 0.5;
};

const makeTextSprite = (text, color) => {
    const canvas = document.createElement("canvas");
    canvas.width = 512;
    canvas.height = 160;
    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.font = "700 42px Instrument Sans, Arial, sans-serif";
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillStyle = color || "#7A3444";
    ctx.fillText(text, canvas.width / 2, canvas.height / 2, 440);

    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    const material = new THREE.SpriteMaterial({
        map: texture,
        transparent: true,
        depthWrite: false,
    });
    const sprite = new THREE.Sprite(material);
    sprite.scale.set(1.75, 0.55, 1);

    return sprite;
};

const makeTextPlane = (text, color, width = 1, height = 0.28) => {
    const canvas = document.createElement("canvas");
    canvas.width = 512;
    canvas.height = 160;
    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.font = "700 42px Instrument Sans, Arial, sans-serif";
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillStyle = color || "#7A3444";
    ctx.fillText(text, canvas.width / 2, canvas.height / 2, 440);

    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    const material = new THREE.MeshBasicMaterial({
        map: texture,
        transparent: true,
        depthWrite: false,
        side: THREE.DoubleSide,
    });
    const mesh = new THREE.Mesh(
        new THREE.PlaneGeometry(width, height),
        material,
    );
    mesh.userData.isTopperText = true;

    return mesh;
};

const makeHeartShape = (radius) => {
    const shape = new THREE.Shape();
    const scale = radius / 16;

    shape.moveTo(0, -10 * scale);
    shape.bezierCurveTo(
        -15 * scale,
        -1 * scale,
        -17 * scale,
        9 * scale,
        -8 * scale,
        13 * scale,
    );
    shape.bezierCurveTo(
        -3 * scale,
        15.5 * scale,
        0,
        12 * scale,
        0,
        8.5 * scale,
    );
    shape.bezierCurveTo(
        0,
        12 * scale,
        3 * scale,
        15.5 * scale,
        8 * scale,
        13 * scale,
    );
    shape.bezierCurveTo(
        17 * scale,
        9 * scale,
        15 * scale,
        -1 * scale,
        0,
        -10 * scale,
    );

    return shape;
};

const makeStarShape = (radius) => {
    const shape = new THREE.Shape();
    for (let i = 0; i < 10; i += 1) {
        const r = i % 2 === 0 ? radius : radius * 0.45;
        const angle = Math.PI / 2 + (i * Math.PI) / 5;
        const x = Math.cos(angle) * r;
        const y = Math.sin(angle) * r;
        if (i === 0) shape.moveTo(x, y);
        else shape.lineTo(x, y);
    }
    shape.closePath();

    return shape;
};

const makeHorizontalExtrude = (shape, depth, material) => {
    const geometry = new THREE.ExtrudeGeometry(shape, {
        depth,
        bevelEnabled: true,
        bevelSize: 0.035,
        bevelThickness: 0.025,
        bevelSegments: 4,
        curveSegments: 32,
    });
    geometry.center();
    const mesh = new THREE.Mesh(geometry, material);
    mesh.rotation.x = -Math.PI / 2;

    return mesh;
};

const createRoundedTier = (shape, radius, height, color, sideColor) => {
    const group = new THREE.Group();
    const topMaterial = new THREE.MeshStandardMaterial({
        color: hexToNumber(color, 0xedd3d6),
        roughness: 0.56,
        metalness: 0,
    });

    if (shape === "Heart") {
        const heartShape = makeHeartShape(radius);
        const sideMaterial = new THREE.MeshStandardMaterial({
            color: hexToNumber(sideColor, 0xd8b7b8),
            roughness: 0.72,
            metalness: 0,
        });
        const body = makeHorizontalExtrude(heartShape, height, sideMaterial);
        group.add(body);

        const top = makeHorizontalExtrude(heartShape, 0.045, topMaterial);
        top.position.y = height / 2 + 0.035;
        group.add(top);

        return group;
    }

    const radialSegments = shape === "Square" ? 4 : 96;
    const bodyGeometry = new THREE.CylinderGeometry(
        radius,
        radius,
        height,
        radialSegments,
        1,
        false,
    );
    const bodyMaterial = new THREE.MeshStandardMaterial({
        color: hexToNumber(sideColor, 0xd8b7b8),
        roughness: 0.72,
        metalness: 0,
    });
    const body = new THREE.Mesh(bodyGeometry, bodyMaterial);
    body.castShadow = false;
    body.receiveShadow = false;
    if (shape === "Square") {
        body.rotation.y = Math.PI / 4;
        body.scale.x = 1.08;
        body.scale.z = 1.08;
    }
    group.add(body);

    const topGeometry = new THREE.CylinderGeometry(
        radius * 1.01,
        radius * 1.01,
        0.045,
        radialSegments,
        1,
        false,
    );
    const top = new THREE.Mesh(topGeometry, topMaterial);
    top.position.y = height / 2 + 0.026;
    if (shape === "Square") {
        top.rotation.y = Math.PI / 4;
        top.scale.x = 1.08;
        top.scale.z = 1.08;
    }
    group.add(top);

    const rimGeometry = new THREE.TorusGeometry(
        radius * 0.98,
        0.045,
        10,
        radialSegments,
    );
    const rim = new THREE.Mesh(rimGeometry, topMaterial);
    rim.rotation.x = Math.PI / 2;
    rim.position.y = height / 2 + 0.055;
    if (shape !== "Square") {
        group.add(rim);
    }

    return group;
};

const getDripStyle = (style, tierIndex = 1) => {
    const tierOffset = ((tierIndex - 1) % 4) * 0.035;
    const styles = {
        curtain: {
            count: 18,
            lengthBase: 0.12 + tierOffset,
            lengthStep: 0.038,
            widthBase: 0.078,
            widthStep: 0.016,
            pool: 0.95,
            bulb: 1.0,
            wobble: 1.0,
        },
        long_uneven: {
            count: 14,
            lengthBase: 0.22 + tierOffset,
            lengthStep: 0.062,
            widthBase: 0.06,
            widthStep: 0.018,
            pool: 0.9,
            bulb: 1.16,
            wobble: 1.35,
        },
        short_subtle: {
            count: 24,
            lengthBase: 0.07 + tierOffset * 0.35,
            lengthStep: 0.022,
            widthBase: 0.052,
            widthStep: 0.01,
            pool: 0.82,
            bulb: 0.72,
            wobble: 0.72,
        },
        heavy_glossy: {
            count: 16,
            lengthBase: 0.18 + tierOffset,
            lengthStep: 0.048,
            widthBase: 0.1,
            widthStep: 0.024,
            pool: 1.02,
            bulb: 1.32,
            wobble: 1.15,
        },
    };

    return styles[style] || styles.curtain;
};

const createRoundDrips = (
    radius,
    height,
    color,
    style = "curtain",
    tierIndex = 1,
) => {
    const group = new THREE.Group();
    const styleConfig = getDripStyle(style, tierIndex);
    const material = new THREE.MeshStandardMaterial({
        color: hexToNumber(color, 0x3f2219),
        roughness: 0.18,
        metalness: 0,
    });

    const rim = new THREE.Mesh(
        new THREE.TorusGeometry(radius * 1.01, 0.042, 12, 96),
        material,
    );
    rim.rotation.x = Math.PI / 2;
    rim.position.y = height / 2 + 0.035;
    group.add(rim);

    const pooledTop = new THREE.Mesh(
        new THREE.CylinderGeometry(
            radius * styleConfig.pool,
            radius * 0.98,
            0.035,
            96,
        ),
        material,
    );
    pooledTop.position.y = height / 2 + 0.052;
    group.add(pooledTop);

    const dripCount = styleConfig.count;
    for (let i = 0; i < dripCount; i += 1) {
        const angle = (i / dripCount) * Math.PI * 2;
        const length =
            styleConfig.lengthBase +
            ((i * 37 + tierIndex * 11) % 9) * styleConfig.lengthStep;
        const width =
            styleConfig.widthBase +
            ((i * 19 + tierIndex * 7) % 5) * styleConfig.widthStep;
        const rows = 5;
        const cols = 4;
        const vertices = [];
        const faces = [];

        for (let row = 0; row < rows; row += 1) {
            const t = row / (rows - 1);
            const rowWidth =
                width * (1 - t * 0.45 + Math.sin(t * Math.PI) * 0.16);
            const y = height / 2 - length * Math.pow(t, 0.9);
            for (let col = 0; col < cols; col += 1) {
                const u = col / (cols - 1) - 0.5;
                const wobble =
                    Math.sin(i * 1.7 + row * 2.1 + col + tierIndex) *
                    0.006 *
                    styleConfig.wobble;
                const a = angle + u * rowWidth + wobble;
                const belly =
                    Math.max(0, 1 - Math.abs(u) * 1.8) *
                    0.016 *
                    Math.sin(t * Math.PI);
                const r = radius + 0.034 + belly;
                vertices.push(Math.cos(a) * r, y, Math.sin(a) * r);
            }
        }

        for (let row = 0; row < rows - 1; row += 1) {
            for (let col = 0; col < cols - 1; col += 1) {
                const a = row * cols + col;
                faces.push(a, a + 1, a + cols + 1);
                faces.push(a, a + cols + 1, a + cols);
            }
        }

        const geometry = new THREE.BufferGeometry();
        geometry.setAttribute(
            "position",
            new THREE.Float32BufferAttribute(vertices, 3),
        );
        geometry.setIndex(faces.flat());
        geometry.computeVertexNormals();
        const dripSheet = new THREE.Mesh(geometry, material);
        group.add(dripSheet);

        const bulbAngle = angle + Math.sin(i * 2.3) * width * 0.18;
        const bulb = new THREE.Mesh(
            new THREE.SphereGeometry(
                (0.045 + ((i * 13) % 4) * 0.007) * styleConfig.bulb,
                18,
                10,
            ),
            material,
        );
        bulb.position.set(
            Math.cos(bulbAngle) * (radius + 0.045),
            height / 2 - length - 0.025,
            Math.sin(bulbAngle) * (radius + 0.045),
        );
        bulb.scale.y = 1.18 + ((i * 11) % 4) * 0.12;
        group.add(bulb);
    }

    return group;
};

const createSquareDrips = (
    radius,
    height,
    color,
    style = "curtain",
    tierIndex = 1,
) => {
    const group = new THREE.Group();
    const styleConfig = getDripStyle(style, tierIndex);
    const material = new THREE.MeshStandardMaterial({
        color: hexToNumber(color, 0x3f2219),
        roughness: 0.18,
        metalness: 0,
    });
    const half = radius * 0.94;

    const topPool = new THREE.Mesh(
        new THREE.BoxGeometry(
            half * 2.05 * styleConfig.pool,
            0.035,
            half * 2.05 * styleConfig.pool,
        ),
        material,
    );
    topPool.position.y = height / 2 + 0.052;
    group.add(topPool);

    const rimPieces = [
        { x: 0, z: half, sx: half * 2.1, sz: 0.055 },
        { x: 0, z: -half, sx: half * 2.1, sz: 0.055 },
        { x: half, z: 0, sx: 0.055, sz: half * 2.1 },
        { x: -half, z: 0, sx: 0.055, sz: half * 2.1 },
    ];
    rimPieces.forEach((piece) => {
        const rim = new THREE.Mesh(
            new THREE.BoxGeometry(piece.sx, 0.085, piece.sz),
            material,
        );
        rim.position.set(piece.x, height / 2 + 0.035, piece.z);
        group.add(rim);
    });

    const sides = [
        { axis: "x", fixed: half, sign: 1 },
        { axis: "x", fixed: -half, sign: -1 },
        { axis: "z", fixed: half, sign: 1 },
        { axis: "z", fixed: -half, sign: -1 },
    ];

    sides.forEach((side, sideIndex) => {
        const count = Math.max(3, Math.round(styleConfig.count / 4));
        for (let i = 0; i < count; i += 1) {
            const along = -half + ((i + 0.55) / count) * half * 2;
            const length =
                styleConfig.lengthBase +
                ((sideIndex * 7 + i * 5 + tierIndex) % 8) *
                    styleConfig.lengthStep;
            const width =
                styleConfig.widthBase * 1.8 +
                ((sideIndex * 3 + i + tierIndex) % 3) * styleConfig.widthStep;
            const wTop = width;
            const wMid = width * 0.78;
            const wTip = width * 0.36;
            const zDepth = 0.026;
            const vertices = [
                -wTop / 2,
                length / 2,
                0,
                wTop / 2,
                length / 2,
                0,
                -wMid / 2,
                -length * 0.12,
                0,
                wMid / 2,
                -length * 0.12,
                0,
                -wTip / 2,
                -length / 2,
                0,
                wTip / 2,
                -length / 2,
                0,
                -wTop / 2,
                length / 2,
                zDepth,
                wTop / 2,
                length / 2,
                zDepth,
                -wMid / 2,
                -length * 0.12,
                zDepth,
                wMid / 2,
                -length * 0.12,
                zDepth,
                -wTip / 2,
                -length / 2,
                zDepth,
                wTip / 2,
                -length / 2,
                zDepth,
            ];
            const indices = [
                0, 1, 3, 0, 3, 2, 2, 3, 5, 2, 5, 4, 6, 9, 7, 6, 8, 9, 8, 11, 9,
                8, 10, 11, 0, 6, 7, 0, 7, 1, 4, 5, 11, 4, 11, 10,
            ];
            const sheetGeometry = new THREE.BufferGeometry();
            sheetGeometry.setAttribute(
                "position",
                new THREE.Float32BufferAttribute(vertices, 3),
            );
            sheetGeometry.setIndex(indices);
            sheetGeometry.computeVertexNormals();
            const sheet = new THREE.Mesh(sheetGeometry, material);
            if (side.axis === "x") {
                sheet.position.set(
                    along,
                    height / 2 - length / 2,
                    side.fixed + side.sign * 0.025,
                );
            } else {
                sheet.position.set(
                    side.fixed + side.sign * 0.025,
                    height / 2 - length / 2,
                    along,
                );
                sheet.rotation.y = Math.PI / 2;
            }
            group.add(sheet);

            const bulb = new THREE.Mesh(
                new THREE.SphereGeometry(
                    (0.04 + (i % 3) * 0.008) * styleConfig.bulb,
                    16,
                    8,
                ),
                material,
            );
            if (side.axis === "x") {
                bulb.position.set(
                    along,
                    height / 2 - length - 0.025,
                    side.fixed + side.sign * 0.035,
                );
            } else {
                bulb.position.set(
                    side.fixed + side.sign * 0.035,
                    height / 2 - length - 0.025,
                    along,
                );
            }
            bulb.scale.y = 1.25;
            group.add(bulb);
        }
    });

    return group;
};

const createHeartDrips = (
    radius,
    height,
    color,
    style = "curtain",
    tierIndex = 1,
) => {
    const group = new THREE.Group();
    const styleConfig = getDripStyle(style, tierIndex);
    const material = new THREE.MeshStandardMaterial({
        color: hexToNumber(color, 0x3f2219),
        roughness: 0.18,
        metalness: 0,
    });

    const top = makeHorizontalExtrude(
        makeHeartShape(radius * 0.94),
        0.035,
        material,
    );
    top.position.y = height / 2 + 0.052;
    group.add(top);

    const count = styleConfig.count + 2;
    for (let i = 0; i < count; i += 1) {
        const angle = (i / count) * Math.PI * 2;
        const heartScale = 1 - 0.18 * Math.max(0, Math.sin(angle));
        const edgeRadius = radius * heartScale;
        const length =
            styleConfig.lengthBase +
            ((i * 29 + tierIndex * 5) % 9) * styleConfig.lengthStep;
        const width =
            styleConfig.widthBase +
            ((i * 17 + tierIndex) % 4) * styleConfig.widthStep;
        const x = Math.cos(angle) * edgeRadius;
        const z = Math.sin(angle) * edgeRadius * 0.82 - radius * 0.08;

        const wTop = width * 1.24;
        const wMid = width * 0.82;
        const wTip = width * 0.34;
        const vertices = [
            -wTop / 2,
            length / 2,
            0,
            wTop / 2,
            length / 2,
            0,
            -wMid / 2,
            -length * 0.1,
            0,
            wMid / 2,
            -length * 0.1,
            0,
            -wTip / 2,
            -length / 2,
            0,
            wTip / 2,
            -length / 2,
            0,
        ];
        const indices = [0, 1, 3, 0, 3, 2, 2, 3, 5, 2, 5, 4];
        const sheetGeometry = new THREE.BufferGeometry();
        sheetGeometry.setAttribute(
            "position",
            new THREE.Float32BufferAttribute(vertices, 3),
        );
        sheetGeometry.setIndex(indices);
        sheetGeometry.computeVertexNormals();
        const sheet = new THREE.Mesh(sheetGeometry, material);
        sheet.position.set(x, height / 2 - length / 2, z);
        sheet.rotation.y = -angle;
        group.add(sheet);

        const bulb = new THREE.Mesh(
            new THREE.SphereGeometry(
                (0.038 + (i % 4) * 0.007) * styleConfig.bulb,
                16,
                8,
            ),
            material,
        );
        bulb.position.set(x, height / 2 - length - 0.025, z);
        bulb.scale.y = 1.2;
        group.add(bulb);
    }

    return group;
};

const createDrips = (
    shape,
    radius,
    height,
    color,
    style = "curtain",
    tierIndex = 1,
) => {
    if (shape === "Square")
        return createSquareDrips(radius, height, color, style, tierIndex);
    if (shape === "Heart")
        return createHeartDrips(radius, height, color, style, tierIndex);
    return createRoundDrips(radius, height, color, style, tierIndex);
};

const dripColors = {
    chocolate: "#3f2219",
    white_chocolate: "#fff6ea",
    pink: "#f26ca1",
    caramel: "#b66a3d",
};

const dripThemeByValue = {
    chocolate: "Chocolate",
    pink: "Strawberry",
    caramel: "Mango",
    white_chocolate: "Mango",
};

const flavorThemeBySponge = {
    Chocolate: "Chocolate",
    Strawberry: "Strawberry",
    "Red Velvet": "Strawberry",
    Lemon: "Mango",
};

const getDripTheme = (state) => {
    if (dripThemeByValue[state.drip]) return dripThemeByValue[state.drip];
    return flavorThemeBySponge[state.sponge] || "Chocolate";
};

const spongeColors = {
    Vanilla: "#f5d7a5",
    Chocolate: "#7b4a38",
    "Red Velvet": "#a43b4a",
    Lemon: "#f3e38a",
    Strawberry: "#f3a6b8",
    Funfetti: "#f7e3b8",
};

const fillingColors = {
    "Chocolate Mousse": "#6a3d2d",
    "Strawberry Jam": "#cf4f6a",
    "Vanilla Cream": "#f6f0dc",
    Nutella: "#5a3528",
    "Cookies & Cream": "#d5d2dd",
    Buttercream: "#f6dfb2",
};

const slugFlavor = (value = "") => String(value).replace(/[^a-z0-9]/gi, "");

const findOozingVariantRoot = (source, state) => {
    const spongeSlug = slugFlavor(state.sponge || "Vanilla");
    const fillingSlug = slugFlavor(state.filling || "Vanilla Cream");
    const roots = [];
    source.traverse((node) => {
        if (
            node.name?.startsWith("WholeCake_") &&
            !node.name.includes("_BottomSponge") &&
            !node.name.includes("_TopSponge") &&
            !node.name.includes("_Filling")
        ) {
            roots.push(node);
        }
    });

    return (
        roots.find(
            (node) =>
                slugFlavor(node.name).includes(spongeSlug) &&
                slugFlavor(node.name).includes(fillingSlug),
        ) ||
        roots.find((node) => slugFlavor(node.name).includes(spongeSlug)) ||
        roots.find((node) => slugFlavor(node.name).includes(fillingSlug)) ||
        roots[0] ||
        source
    );
};

const getOozingMeshKind = (node) => {
    const objectName = String(node.name || "").toLowerCase();
    const materialName = String(node.material?.name || "").toLowerCase();
    const combinedName = `${objectName} ${materialName}`;

    if (
        objectName.includes("bottomsponge") ||
        objectName.includes("topsponge") ||
        objectName.includes("_sponge") ||
        objectName.includes("sponge")
    ) {
        return "sponge";
    }

    if (
        objectName.includes("filling") ||
        objectName.includes("ooze") ||
        objectName.includes("drip")
    ) {
        return "filling";
    }

    if (combinedName.includes("frost") || combinedName.includes("icing")) {
        return "frosting";
    }

    if (
        materialName.includes("mousse") ||
        materialName.includes("jam") ||
        materialName.includes("nutella") ||
        materialName.includes("cream")
    ) {
        return "filling";
    }

    if (combinedName.includes("cake")) {
        return "sponge";
    }

    return "other";
};

const prepareOozingInsideModel = (source, state) => {
    const root = findOozingVariantRoot(source, state);
    const rootName = root.name || "";
    const rootPrefix = rootName ? `${rootName}_` : "";
    const group = new THREE.Group();
    const clone = root.clone(true);
    let meshCount = 0;
    clone.traverse((node) => {
        if (node.isMesh) meshCount += 1;
    });

    if (meshCount > 0) {
        group.add(clone);
    } else if (rootPrefix) {
        source.traverse((node) => {
            if (node.isMesh && node.name?.startsWith(rootPrefix)) {
                group.add(node.clone(true));
            }
        });
    } else {
        source.traverse((node) => {
            if (node.isMesh) group.add(node.clone(true));
        });
    }

    const spongeColor = hexToNumber(
        state.spongeColor || spongeColors[state.sponge] || "#f5d7a5",
        0xf5d7a5,
    );
    const fillingColor = hexToNumber(
        state.fillingColor || fillingColors[state.filling] || "#f6f0dc",
        0xf6f0dc,
    );
    const frostingColor = hexToNumber(state.frostingTop || "#e9e2cf", 0xe9e2cf);

    group.traverse((node) => {
        if (!node.isMesh) return;
        node.castShadow = false;
        node.receiveShadow = false;
        if (!node.material) return;
        node.material = node.material.clone();
        const meshKind = getOozingMeshKind(node);
        if (meshKind === "filling") {
            node.material.map = null;
            node.material.color.set(fillingColor);
            node.material.roughness = 0.12;
            node.material.metalness = 0;
            if ("clearcoat" in node.material) node.material.clearcoat = 0.85;
            if ("clearcoatRoughness" in node.material)
                node.material.clearcoatRoughness = 0.04;
        } else if (meshKind === "sponge") {
            node.material.map = null;
            node.material.color.set(spongeColor);
            node.material.roughness = 0.78;
            node.material.metalness = 0;
        } else if (meshKind === "frosting") {
            node.material.map = null;
            node.material.color.set(frostingColor);
            node.material.roughness = 0.46;
            node.material.metalness = 0;
        }
        node.material.needsUpdate = true;
    });

    let hasMesh = false;
    group.traverse((node) => {
        if (node.isMesh) hasMesh = true;
    });
    if (!hasMesh) {
        const fallback = new THREE.Mesh(
            new THREE.BoxGeometry(1.8, 0.68, 1.05),
            new THREE.MeshStandardMaterial({
                color: spongeColor,
                roughness: 0.76,
            }),
        );
        group.add(fallback);
    }

    group.updateWorldMatrix(true, true);
    const box = new THREE.Box3().setFromObject(group);
    const size = box.getSize(new THREE.Vector3());
    const center = box.getCenter(new THREE.Vector3());
    const maxDimension = Math.max(size.x, size.y, size.z, 0.001);
    const scale = 2.45 / maxDimension;

    group.children.forEach((child) => {
        child.position.x -= center.x;
        child.position.z -= center.z;
        child.position.y -= box.min.y;
    });

    group.scale.setScalar(scale);
    group.position.set(0, -0.76, 0);
    group.rotation.y = -0.18;
    group.userData.fromOozingInsideAsset = true;

    return group;
};

const createTopper = (type, message = "", textColor = "#7A3444") => {
    const group = new THREE.Group();
    const stickMaterial = new THREE.MeshStandardMaterial({
        color: 0xc7935d,
        roughness: 0.55,
    });
    const blankMaterial = new THREE.MeshStandardMaterial({
        color: 0xfffbf4,
        roughness: 0.48,
    });
    const goldMaterial = new THREE.MeshStandardMaterial({
        color: 0xf2b84c,
        roughness: 0.24,
        metalness: 0.45,
    });
    const acrylicMaterial = new THREE.MeshPhysicalMaterial({
        color: 0xdff7ff,
        roughness: 0.05,
        metalness: 0,
        transparent: true,
        opacity: 0.46,
        transmission: 0.35,
    });

    const stickXs = type === "acrylic" ? [0] : [-0.34, 0.34];
    stickXs.forEach((x) => {
        const stick = new THREE.Mesh(
            new THREE.CylinderGeometry(0.018, 0.018, 0.62, 12),
            stickMaterial,
        );
        stick.position.set(x, 0.15, 0);
        group.add(stick);
    });

    if (type === "acrylic") {
        const plaque = new THREE.Mesh(
            new THREE.CylinderGeometry(0.38, 0.38, 0.035, 72),
            acrylicMaterial,
        );
        plaque.rotation.x = Math.PI / 2;
        plaque.position.y = 0.56;
        group.add(plaque);

        const edge = new THREE.Mesh(
            new THREE.TorusGeometry(0.38, 0.012, 8, 72),
            new THREE.MeshStandardMaterial({
                color: 0xf3fdff,
                roughness: 0.18,
                transparent: true,
                opacity: 0.72,
            }),
        );
        edge.position.y = 0.56;
        edge.rotation.x = Math.PI / 2;
        group.add(edge);

        if (message) {
            const text = makeTextPlane(
                String(message).slice(0, 24),
                textColor,
                0.72,
                0.22,
            );
            text.position.set(0, 0.56, 0.055);
            group.add(text);
        }

        return group;
    }

    const signMaterial = type === "name" ? goldMaterial : blankMaterial;
    const sign = new THREE.Mesh(
        new THREE.BoxGeometry(1.05, type === "name" ? 0.22 : 0.46, 0.045),
        signMaterial,
    );
    sign.position.y = type === "name" ? 0.54 : 0.58;
    group.add(sign);

    if (message) {
        const text = makeTextPlane(
            String(message).slice(0, 24),
            textColor,
            0.92,
            type === "name" ? 0.18 : 0.26,
        );
        text.position.set(0, sign.position.y, 0.03);
        group.add(text);
    }

    return group;
};

const getToppingFootprint = (item) => {
    const scale = Number(item?.scale || 1);
    const shape = item?.shape || "dot";
    const footprintMap = {
        sprinkle: Math.max(0.14, Number(item?.length || 14) * 0.01 * scale),
        heart: 0.17 * scale,
        flower: 0.18 * scale,
        star: 0.16 * scale,
        chip: 0.1 * scale,
        nut: 0.12 * scale,
        dot: 0.08 * scale,
    };

    return footprintMap[shape] || footprintMap.dot;
};

const getToppingSafeFactor = (radius, item) => {
    const usableRadius = Math.max(0.35, Number(radius || 1));
    const footprint = getToppingFootprint(item);
    const edgePadding = 0.12;
    return Math.max(
        0.28,
        Math.min(0.82, 1 - footprint / usableRadius - edgePadding),
    );
};

const normalizedPointInsideShape = (shape, x, z, safeFactor = 0.64) => {
    if (shape === "Square") {
        return Math.abs(x) <= safeFactor && Math.abs(z) <= safeFactor;
    }

    if (shape === "Heart") {
        const safe = Math.max(0.35, Math.min(1, safeFactor || 0.64));
        const heartWidthLimit = safe * 0.82;
        const heartUpperLimit = safe * 0.66;
        const heartLowerPointLimit = safe * 0.42;
        if (
            Math.abs(x) > heartWidthLimit ||
            z < -heartUpperLimit ||
            z > heartLowerPointLimit
        ) {
            return false;
        }
        const px = 160 + (x / safe) * 108;
        const py = 160 + (z / safe) * 108;
        const nx = (px - 160) / 88;
        const ny = (py - 154) / 76;
        const v =
            Math.pow(nx * nx + ny * ny - 1, 3) - nx * nx * Math.pow(ny, 3);
        const lowerPointInset = py > 198 ? (py - 198) * 0.95 : 0;
        return (
            v <= -0.012 &&
            py >= 92 &&
            py <= 222 &&
            px >= 72 + lowerPointInset &&
            px <= 248 - lowerPointInset
        );
    }

    return x * x + z * z <= safeFactor * safeFactor;
};

const constrainTopPoint = (shape, x, z, safeFactor) => {
    let nx = Number.isFinite(x) ? x : 0;
    let nz = Number.isFinite(z) ? z : 0;

    if (shape === "Square") {
        return {
            x: Math.max(-safeFactor, Math.min(safeFactor, nx)),
            z: Math.max(-safeFactor, Math.min(safeFactor, nz)),
        };
    }

    if (normalizedPointInsideShape(shape, nx, nz, safeFactor)) {
        return { x: nx, z: nz };
    }

    for (let scale = 0.96; scale >= 0.05; scale -= 0.04) {
        const tx = nx * scale;
        const tz = nz * scale;
        if (normalizedPointInsideShape(shape, tx, tz, safeFactor)) {
            return { x: tx, z: tz };
        }
    }

    return { x: 0, z: shape === "Heart" ? -0.08 : 0 };
};

const topPointToWorld = (item, radius, shape) => {
    const rawX = ((Number(item.x) || 160) - 160) / 108;
    const rawZ = ((Number(item.y) || 160) - 160) / 108;
    const safeFactor =
        getToppingSafeFactor(radius, item) * (shape === "Heart" ? 0.82 : 1);
    const { x, z } = constrainTopPoint(shape, rawX, rawZ, safeFactor);
    const shapeScale = shape === "Heart" ? 0.9 : 1;

    return {
        x: x * radius * shapeScale,
        z: z * radius * shapeScale,
    };
};

const createTopping = (item) => {
    const group = new THREE.Group();
    const color = hexToNumber(item.color || "#ff7eac", 0xff7eac);
    const material = new THREE.MeshStandardMaterial({
        color,
        roughness: 0.42,
        metalness: 0,
    });
    const shape = item.shape || "dot";
    const scale = Number(item.scale || 1);

    if (shape === "sprinkle") {
        const sprinkle = new THREE.Mesh(
            new THREE.CapsuleGeometry(0.018 * scale, 0.18 * scale, 4, 10),
            material,
        );
        sprinkle.rotation.z = Math.PI / 2;
        sprinkle.rotation.y = ((Number(item.rotation) || 0) * Math.PI) / 180;
        group.add(sprinkle);
        return group;
    }

    if (shape === "heart") {
        const heart = makeHorizontalExtrude(
            makeHeartShape(0.13),
            0.035,
            material,
        );
        heart.scale.set(0.85 * scale, 0.85 * scale, 0.85 * scale);
        group.add(heart);
        return group;
    }

    if (shape === "flower") {
        const petalMaterial = material;
        for (let i = 0; i < 10; i += 1) {
            const angle = (i / 10) * Math.PI * 2;
            const petal = new THREE.Mesh(
                new THREE.SphereGeometry(0.047 * scale, 14, 8),
                petalMaterial,
            );
            petal.position.set(
                Math.cos(angle) * 0.076 * scale,
                0.004,
                Math.sin(angle) * 0.076 * scale,
            );
            petal.scale.set(1.25, 0.3, 0.66);
            petal.rotation.y = angle;
            group.add(petal);
        }
        for (let i = 0; i < 6; i += 1) {
            const angle = ((i + 0.5) / 6) * Math.PI * 2;
            const petal = new THREE.Mesh(
                new THREE.SphereGeometry(0.035 * scale, 12, 8),
                petalMaterial,
            );
            petal.position.set(
                Math.cos(angle) * 0.035 * scale,
                0.025,
                Math.sin(angle) * 0.035 * scale,
            );
            petal.scale.set(1.05, 0.32, 0.58);
            group.add(petal);
        }
        const center = new THREE.Mesh(
            new THREE.SphereGeometry(0.032 * scale, 12, 8),
            new THREE.MeshStandardMaterial({
                color: 0xffdf70,
                roughness: 0.38,
            }),
        );
        center.position.y = 0.045;
        group.add(center);
        return group;
    }

    if (shape === "star") {
        const star = makeHorizontalExtrude(
            makeStarShape(0.125 * scale),
            0.035 * scale,
            material,
        );
        group.add(star);
        return group;
    }

    if (shape === "chip") {
        const chip = new THREE.Mesh(
            new THREE.ConeGeometry(0.065 * scale, 0.08 * scale, 18),
            material,
        );
        chip.scale.set(1.05, 0.72, 1.05);
        group.add(chip);
        return group;
    }

    if (shape === "nut") {
        const nut = new THREE.Mesh(
            new THREE.SphereGeometry(0.06 * scale, 14, 8),
            material,
        );
        nut.scale.set(1.65, 0.34, 0.72);
        nut.rotation.y = ((Number(item.rotation) || 0) * Math.PI) / 180;
        group.add(nut);
        return group;
    }

    const pearl = new THREE.Mesh(
        new THREE.SphereGeometry(0.052 * scale, 18, 12),
        material,
    );
    group.add(pearl);
    return group;
};

const getToppingLayoutKind = (item) => {
    if (item?.preset === "chips" || item?.shape === "chip")
        return "ChocolateChip";
    if (item?.preset === "nuts" || item?.shape === "nut") return "Nut";
    if (item?.preset === "pearls") return "PearlCandy";
    if (item?.preset === "sprinkles_choco") return "ChocolateSprinkle";
    if (item?.preset === "sprinkles_white") return "WhiteSprinkle";
    if (item?.shape === "sprinkle") return "Sprinkle";
    if (item?.shape === "heart") return "Heart";
    if (item?.shape === "flower") return "Flower";
    if (item?.shape === "star") return "Star";
    return "Dot";
};

const getToppingLayoutBaseName = (name = "") =>
    String(name)
        .replace(/_(Body|EndA|EndB|Center)$/i, "")
        .replace(/_Petal_\\d+$/i, "");

const buildToppingLayoutLibrary = (assetScene) => {
    const library = new Map();
    const allMeshes = [];
    assetScene.traverse((node) => {
        if (!node.isMesh) return;
        allMeshes.push(node);
        const baseName = getToppingLayoutBaseName(node.name);
        if (!library.has(baseName)) {
            library.set(baseName, []);
        }
        library.get(baseName).push(node);
    });

    const fullBox = new THREE.Box3();
    allMeshes.forEach((mesh) => fullBox.expandByObject(mesh));
    return { library, fullBox };
};

const cloneLayoutTopping = (sourceMeshes, item, index) => {
    const group = new THREE.Group();
    const color = hexToNumber(item.color || "#ff7eac", 0xff7eac);
    sourceMeshes.forEach((source) => {
        const clone = source.clone(true);
        clone.geometry = source.geometry;
        if (clone.material) {
            clone.material = clone.material.clone();
            const materialName = String(
                clone.material.name || clone.name || "",
            ).toLowerCase();
            if (materialName.includes("recolorable")) {
                clone.material.color.set(color);
            }
            clone.material.roughness = Math.min(
                0.8,
                clone.material.roughness ?? 0.42,
            );
            clone.material.metalness = 0;
        }
        clone.castShadow = false;
        clone.receiveShadow = false;
        group.add(clone);
    });
    group.name = `Customizer3D_LayoutTopping_${getToppingLayoutKind(item)}_${index}`;
    return group;
};

const pickLayoutMeshes = (layoutLibrary, kind, index) => {
    const candidates = [...layoutLibrary.entries()].filter(([name]) =>
        name.includes(`Topping${kind}_`),
    );

    if (candidates.length === 0 && kind === "ChocolateSprinkle") {
        return pickLayoutMeshes(layoutLibrary, "Sprinkle", index);
    }
    if (candidates.length === 0 && kind === "WhiteSprinkle") {
        return pickLayoutMeshes(layoutLibrary, "Sprinkle", index);
    }
    if (candidates.length === 0 && kind === "PearlCandy") {
        return pickLayoutMeshes(layoutLibrary, "Dot", index);
    }
    if (candidates.length === 0) return null;

    const [, meshes] = candidates[index % candidates.length];
    return meshes;
};

const materialLooksLikeDrip = (name) =>
    name.includes("drip") ||
    name.includes("chocolate") ||
    name.includes("ganache") ||
    name.includes("cap") ||
    name.includes("skirt") ||
    name.includes("band") ||
    name.includes("round_photo") ||
    name.includes("copied_circle_style");

const materialLooksLikeCakeBody = (name) =>
    name.includes("body") ||
    name.includes("cake") ||
    name.includes("frosting") ||
    name.includes("cream") ||
    name.includes("cylinder");

const materialLooksLikeExportBase = (name) =>
    name.includes("board") ||
    name.includes("plate") ||
    name.includes("stand") ||
    name.includes("pedestal");

const prepareBlenderCakeModel = (
    source,
    state,
    topColor,
    sideColor,
    dripColor,
) => {
    const group = source.clone(true);
    group.userData.fromBlenderCakeAsset = true;
    const initialBox = new THREE.Box3().setFromObject(group);

    group.traverse((node) => {
        if (!node.isMesh) return;
        node.castShadow = false;
        node.receiveShadow = false;
        node.userData.fromBlenderCakeAsset = true;
        const objectName =
            `${node.name || ""} ${node.material?.name || ""}`.toLowerCase();
        const meshBox = new THREE.Box3().setFromObject(node);
        const meshSize = meshBox.getSize(new THREE.Vector3());
        const isFlatBottomBase =
            meshSize.y > 0 &&
            meshSize.y < Math.max(meshSize.x, meshSize.z) * 0.08 &&
            meshBox.min.y <= initialBox.min.y + meshSize.y * 2.2;
        if (materialLooksLikeExportBase(objectName) || isFlatBottomBase) {
            node.visible = false;
            return;
        }
        if (node.material) {
            node.material = node.material.clone();
            node.material.roughness = materialLooksLikeDrip(objectName)
                ? 0.16
                : 0.52;
            node.material.metalness = 0;
            if (
                materialLooksLikeDrip(objectName) &&
                state.drip &&
                state.drip !== "none"
            ) {
                node.material.color.set(hexToNumber(dripColor, 0x3f2219));
                node.visible = true;
            } else if (materialLooksLikeDrip(objectName)) {
                node.visible = false;
            } else if (materialLooksLikeCakeBody(objectName)) {
                node.material.color.set(
                    hexToNumber(sideColor || topColor, 0xe9e2cf),
                );
            }
        }
    });

    const box = new THREE.Box3().setFromObject(group);
    const size = box.getSize(new THREE.Vector3());
    const center = box.getCenter(new THREE.Vector3());
    const maxDimension = Math.max(size.x, size.z, size.y, 0.001);
    const sizeScale =
        getCakeSizeScale(state.size) * getShapeVisualScale(state.shape);
    const scale = (2.92 / maxDimension) * sizeScale;
    group.scale.setScalar(scale);
    group.position.set(
        -center.x * scale,
        -(box.min.y * scale) - 0.84,
        -center.z * scale,
    );
    group.updateWorldMatrix(true, true);
    const fittedBox = new THREE.Box3().setFromObject(group);
    const layers = Math.max(1, Math.min(4, Number(state.layers || 1)));
    const fittedSize = fittedBox.getSize(new THREE.Vector3());
    const fittedHalfWidth = Math.min(fittedSize.x, fittedSize.z) / 2;
    const topTierRadius = fittedHalfWidth * getTopTierVisibleFactor(layers);
    group.userData.topY = fittedBox.max.y;
    group.userData.toppingRadius = Math.max(0.32, topTierRadius);
    group.userData.sizeScale = sizeScale;

    return group;
};

const addProceduralToppings = (cakeGroup, toppings, shape, topY, radius) => {
    if (!Array.isArray(toppings) || toppings.length === 0) return;
    const topRadius = Math.max(0.35, Number(radius || 1));

    toppings.slice(0, 90).forEach((item, index) => {
        const { x, z } = topPointToWorld(item, topRadius, shape);
        const topping = createTopping(item);
        topping.name = `Customizer3D_Topping_${item.shape || "dot"}_${index}`;
        topping.position.set(x, topY + 0.09 + (index % 4) * 0.003, z);
        topping.rotation.y =
            ((Number(item.rotation) || index * 23) * Math.PI) / 180;
        cakeGroup.add(topping);
    });
};

const addToppings = (
    cakeGroup,
    toppings,
    shape,
    topY,
    radius,
    layers = 1,
    options = {},
) => {
    if (!Array.isArray(toppings) || toppings.length === 0) return;
    const isCurrent = options.isCurrent || (() => true);
    const topRadius = Math.max(0.35, Number(radius || 1));

    loadToppingLayoutAsset(shape, layers)
        .then((asset) => {
            if (!isCurrent()) return;
            const { library, fullBox } = buildToppingLayoutLibrary(asset.scene);
            if (library.size === 0 || fullBox.isEmpty()) {
                addProceduralToppings(
                    cakeGroup,
                    toppings,
                    shape,
                    topY,
                    topRadius,
                );
                return;
            }

            const fullSize = fullBox.getSize(new THREE.Vector3());
            const fullCenter = fullBox.getCenter(new THREE.Vector3());
            const layoutDiameter = Math.max(fullSize.x, fullSize.z, 0.001);
            const layoutScale = (topRadius * 2) / layoutDiameter;
            const layoutGroup = new THREE.Group();
            const typeUsage = {};

            toppings.slice(0, 90).forEach((item, index) => {
                const kind = getToppingLayoutKind(item);
                const usage = typeUsage[kind] || 0;
                typeUsage[kind] = usage + 1;
                const sourceMeshes = pickLayoutMeshes(library, kind, usage);
                if (!sourceMeshes) return;
                layoutGroup.add(cloneLayoutTopping(sourceMeshes, item, index));
            });

            if (layoutGroup.children.length === 0) {
                addProceduralToppings(
                    cakeGroup,
                    toppings,
                    shape,
                    topY,
                    topRadius,
                );
                return;
            }

            layoutGroup.children.forEach((child) => {
                child.position.x -= fullCenter.x;
                child.position.z -= fullCenter.z;
                child.position.y -= fullBox.min.y;
            });
            layoutGroup.scale.setScalar(layoutScale);
            layoutGroup.position.set(0, topY + 0.045, 0);
            layoutGroup.name = `Customizer3D_ToppingLayout_${shape}_${layers}`;
            cakeGroup.add(layoutGroup);
        })
        .catch(() => {
            if (isCurrent())
                addProceduralToppings(
                    cakeGroup,
                    toppings,
                    shape,
                    topY,
                    topRadius,
                );
        });
};

const buildDripVariant = (
    dripVariantScene,
    shape,
    tierIndex,
    color,
    radius,
    tierHeight,
) => {
    if (!dripVariantScene) return null;

    const variantPrefix = `CustomizerCake_DripShapeTier_${shape}_Tier${tierIndex}_`;
    const sourceObjects = [];
    dripVariantScene.traverse((node) => {
        if (node.isMesh && node.name.includes(variantPrefix)) {
            sourceObjects.push(node);
        }
    });

    if (sourceObjects.length === 0) return null;

    const group = new THREE.Group();
    group.userData.fromDripVariantAsset = true;
    sourceObjects.forEach((source) => {
        const clone = source.clone(true);
        clone.userData.fromDripVariantAsset = true;
        clone.castShadow = false;
        clone.receiveShadow = false;
        if (clone.material) {
            clone.material = clone.material.clone();
            if (clone.name.includes("Highlight")) {
                clone.material.color.set(hexToNumber(color, 0x3f2219));
                clone.material.opacity = 0.55;
                clone.material.transparent = true;
            } else {
                clone.material.color.set(hexToNumber(color, 0x3f2219));
                clone.material.roughness = 0.16;
                clone.material.metalness = 0;
            }
        }
        group.add(clone);
    });

    const initialBox = new THREE.Box3().setFromObject(group);
    const initialSize = initialBox.getSize(new THREE.Vector3());
    const targetDiameter = radius * 2.08;
    const currentDiameter = Math.max(initialSize.x, initialSize.z, 0.001);
    group.scale.setScalar(targetDiameter / currentDiameter);

    const box = new THREE.Box3().setFromObject(group);
    const center = box.getCenter(new THREE.Vector3());
    const desiredTop = tierHeight / 2 + 0.095;
    group.position.set(-center.x, desiredTop - box.max.y, -center.z);

    return group;
};

const initCustomizer3D = () => {
    const host = document.getElementById("cake-3d-canvas");
    if (!host) return;
    const isImmersive = Boolean(document.getElementById("test-customize-page"));

    const scene = new THREE.Scene();
    scene.background = null;

    const camera = new THREE.PerspectiveCamera(
        isImmersive ? 33 : 36,
        1,
        0.1,
        100,
    );
    camera.position.set(0, isImmersive ? 4.4 : 3.7, isImmersive ? 14.5 : 6.2);
    camera.lookAt(0, isImmersive ? 0.15 : 1.05, 0);

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.shadowMap.enabled = false;
    host.appendChild(renderer.domElement);

    const ambient = new THREE.HemisphereLight(
        0xffffff,
        0xf1d9df,
        isImmersive ? 2.8 : 2.4,
    );
    scene.add(ambient);

    const key = new THREE.DirectionalLight(0xffffff, 2.25);
    key.position.set(3.5, 6, isImmersive ? 5.8 : 4.5);
    scene.add(key);

    const fill = new THREE.PointLight(0xf6b6c6, 1.2, 10);
    fill.position.set(-3, 2, 2);
    scene.add(fill);

    const turntableGroup = new THREE.Group();
    scene.add(turntableGroup);

    const cakeGroup = new THREE.Group();
    turntableGroup.add(cakeGroup);

    const insideGroup = new THREE.Group();
    insideGroup.visible = false;
    turntableGroup.add(insideGroup);

    const stageGroup = new THREE.Group();
    turntableGroup.add(stageGroup);

    const shadowTextureCanvas = document.createElement("canvas");
    shadowTextureCanvas.width = 256;
    shadowTextureCanvas.height = 256;
    const shadowCtx = shadowTextureCanvas.getContext("2d");
    const shadowGradient = shadowCtx.createRadialGradient(
        128,
        128,
        12,
        128,
        128,
        112,
    );
    shadowGradient.addColorStop(0, "rgba(90, 58, 58, 0.28)");
    shadowGradient.addColorStop(0.55, "rgba(90, 58, 58, 0.13)");
    shadowGradient.addColorStop(1, "rgba(90, 58, 58, 0)");
    shadowCtx.fillStyle = shadowGradient;
    shadowCtx.fillRect(0, 0, 256, 256);
    const shadowTexture = new THREE.CanvasTexture(shadowTextureCanvas);
    shadowTexture.colorSpace = THREE.SRGBColorSpace;
    const floorShadow = new THREE.Mesh(
        new THREE.PlaneGeometry(3.8, 2.15),
        new THREE.MeshBasicMaterial({
            map: shadowTexture,
            transparent: true,
            depthWrite: false,
        }),
    );
    floorShadow.rotation.x = -Math.PI / 2;
    floorShadow.position.set(0, -0.9, 0);
    scene.add(floorShadow);

    if (isImmersive) {
        const plateMaterial = new THREE.MeshStandardMaterial({
            color: 0xfff8fa,
            roughness: 0.38,
            metalness: 0,
        });
        const plate = new THREE.Mesh(
            new THREE.CylinderGeometry(2.55, 2.75, 0.12, 128),
            plateMaterial,
        );
        plate.position.y = -0.94;
        stageGroup.add(plate);

        const ringMaterial = new THREE.MeshBasicMaterial({
            color: 0xe6b7be,
            transparent: true,
            opacity: 0.28,
        });
        [2.9, 3.45].forEach((radius, index) => {
            const ring = new THREE.Mesh(
                new THREE.TorusGeometry(radius, 0.012, 8, 160),
                ringMaterial.clone(),
            );
            ring.rotation.x = Math.PI / 2;
            ring.position.y = -0.87 - index * 0.015;
            stageGroup.add(ring);
        });
    }

    let state = window.__bonbonCustomize3DState || {};
    let rafId = null;
    let autoRotate = true;
    let viewYaw = 0;
    let viewPitch = 0;
    let isDragging = false;
    let userControlledView = false;
    let lastPointer = { x: 0, y: 0 };
    let buildSequence = 0;
    let insideSequence = 0;
    let activeView = "auto";

    const getCameraLayout = () => {
        const width = Math.max(260, host.clientWidth || 320);
        const height = Math.max(300, host.clientHeight || 330);
        const aspect = width / height;

        let device = "desktop";
        if (width < 640 && aspect < 1) {
            device = "phonePortrait";
        } else if (width < 900 && aspect >= 1) {
            device = "phoneLandscape";
        } else if (width < 1024) {
            device = "tablet";
        } else if (width >= 1440) {
            device = "wideDesktop";
        }

        const presets = {
            phonePortrait: {
                front: { distance: 8.8, height: 3.55, lookY: 0.18, fov: 46 },
                side: { distance: 8.8, height: 3.55, lookY: 0.18, fov: 46 },
                top: { height: 10.6, lookY: 0, fov: 50 },
                inside: { distance: 7.4, height: 3.05, lookY: 0, fov: 46 },
            },
            phoneLandscape: {
                front: { distance: 7.4, height: 3.15, lookY: 0.12, fov: 42 },
                side: { distance: 7.4, height: 3.15, lookY: 0.12, fov: 42 },
                top: { height: 8.8, lookY: 0, fov: 44 },
                inside: { distance: 6.7, height: 2.8, lookY: 0, fov: 42 },
            },
            tablet: {
                front: { distance: 8.2, height: 3.65, lookY: 0.16, fov: 40 },
                side: { distance: 8.2, height: 3.65, lookY: 0.16, fov: 40 },
                top: { height: 9.4, lookY: 0, fov: 43 },
                inside: { distance: 7.0, height: 3.0, lookY: 0, fov: 40 },
            },
            desktop: {
                front: { distance: 12.2, height: 4.35, lookY: 0.15, fov: 35 },
                side: { distance: 12.2, height: 4.35, lookY: 0.15, fov: 35 },
                top: { height: 10.2, lookY: 0, fov: 38 },
                inside: { distance: 7.9, height: 3.2, lookY: 0, fov: 37 },
            },
            wideDesktop: {
                front: { distance: 13.2, height: 4.55, lookY: 0.12, fov: 33 },
                side: { distance: 13.2, height: 4.55, lookY: 0.12, fov: 33 },
                top: { height: 10.8, lookY: 0, fov: 36 },
                inside: { distance: 8.4, height: 3.25, lookY: 0, fov: 35 },
            },
        };

        return {
            width,
            height,
            aspect,
            device,
            preset: presets[device],
            centerPanX: 0,
        };
    };

    const updateCamera = () => {
        const inside = activeView === "inside";
        const top = activeView === "top" || viewPitch > 2.5;
        const layout = getCameraLayout();
        const viewKey = inside
            ? "inside"
            : top
              ? "top"
              : activeView === "side"
                ? "side"
                : "front";
        const preset = isImmersive
            ? layout.preset[viewKey]
            : {
                  front: { distance: 6.2, height: 3.7, lookY: 1.05, fov: 36 },
                  side: { distance: 6.2, height: 3.7, lookY: 1.05, fov: 36 },
                  top: { height: 7.8, lookY: 0, fov: 36 },
                  inside: { distance: 5.4, height: 2.5, lookY: 0.35, fov: 36 },
              }[viewKey];
        const panX = layout.centerPanX;

        camera.fov = preset.fov;
        camera.updateProjectionMatrix();

        if (top) {
            camera.position.set(panX, preset.height, 0.08);
            camera.lookAt(panX, preset.lookY, 0);
        } else {
            const height = preset.height + viewPitch;
            camera.position.set(panX, height, preset.distance);
            camera.lookAt(panX, preset.lookY, 0);
        }
    };

    const syncViewButtons = (activeView = autoRotate ? "auto" : "") => {
        document.querySelectorAll("[data-cake-view]").forEach((button) => {
            const active = button.dataset.cakeView === activeView;
            button.classList.toggle("border-[#ec5a61]", active);
            button.classList.toggle("bg-[#FDECEF]", active);
            button.classList.toggle("text-[#5A3A3A]", active);
            button.classList.toggle("border-[#F3D7DB]", !active);
            button.classList.toggle("bg-white/90", !active);
            button.classList.toggle("text-[#6E4D53]", !active);
        });
    };

    const setView = (view, options = {}) => {
        if (options.userInitiated) {
            userControlledView = true;
        }

        activeView = view;
        cakeGroup.visible = view !== "inside";
        insideGroup.visible = view === "inside";

        if (view === "auto") {
            autoRotate = true;
            viewPitch = 0;
            cakeGroup.visible = true;
            insideGroup.visible = false;
            updateCamera();
            syncViewButtons("auto");
            return;
        }

        autoRotate = true;
        viewPitch = view === "top" ? 3.05 : 0;
        if (view === "front") viewYaw = 0;
        if (view === "side") viewYaw = Math.PI / 2;
        if (view === "top") viewYaw = 0;
        if (view === "inside") {
            viewYaw = 0;
            rebuildInsideModel(state);
        }
        updateCamera();
        syncViewButtons(view);
    };

    const clearInside = () => {
        while (insideGroup.children.length) {
            const child = insideGroup.children[0];
            insideGroup.remove(child);
            child.traverse?.((node) => {
                node.geometry?.dispose?.();
                if (Array.isArray(node.material)) {
                    node.material.forEach((material) => material.dispose?.());
                } else {
                    node.material?.dispose?.();
                }
            });
        }
    };

    const clearCake = () => {
        while (cakeGroup.children.length) {
            const child = cakeGroup.children[0];
            cakeGroup.remove(child);
            child.traverse?.((node) => {
                if (node.userData?.fromDripVariantAsset) return;
                if (!node.userData?.fromBlenderCakeAsset) {
                    node.geometry?.dispose?.();
                }
                if (Array.isArray(node.material)) {
                    node.material.forEach((material) => {
                        material.map?.dispose?.();
                        material.dispose?.();
                    });
                } else {
                    node.material?.map?.dispose?.();
                    node.material?.dispose?.();
                }
            });
        }
    };

    const rebuildInsideModel = (nextState = {}) => {
        const insideState = { ...state, ...nextState };
        const sequence = ++insideSequence;
        clearInside();

        loadOozingInsideAsset()
            .then((asset) => {
                if (sequence !== insideSequence) return;
                clearInside();
                const model = prepareOozingInsideModel(
                    asset.scene,
                    insideState,
                );
                insideGroup.add(model);
            })
            .catch(() => {
                if (sequence !== insideSequence) return;
                clearInside();
                const fallback = new THREE.Mesh(
                    new THREE.BoxGeometry(1.8, 0.7, 1.05),
                    new THREE.MeshStandardMaterial({
                        color: hexToNumber(
                            spongeColors[insideState.sponge] || "#f5d7a5",
                            0xf5d7a5,
                        ),
                        roughness: 0.76,
                    }),
                );
                fallback.position.y = -0.38;
                insideGroup.add(fallback);
            });
    };

    const rebuildCake = (nextState = {}) => {
        state = { ...state, ...nextState };
        window.__bonbonCustomize3DState = state;
        clearCake();
        if (activeView === "inside") {
            rebuildInsideModel(state);
        }

        const layers = Math.max(1, Math.min(4, Number(state.layers || 1)));
        const size = Number(state.size || 6);
        const shape = state.shape || "Round";
        const topColor = state.frostingTop || "#e9e2cf";
        const sideColor = state.frostingBottom || darkenHex(topColor, 0.78);
        const sizeScale = getCakeSizeScale(size) * getShapeVisualScale(shape);
        const baseRadius = 1.16 * sizeScale;
        const tierHeight = 0.55;
        const dripColor = dripColors[state.drip] || "#3f2219";
        const sequence = ++buildSequence;

        if (isImmersive && !state.useProceduralFallback) {
            loadBlenderCakeAsset(shape, layers)
                .then((asset) => {
                    if (sequence !== buildSequence) return;
                    clearCake();
                    const blenderCake = prepareBlenderCakeModel(
                        asset.scene,
                        state,
                        topColor,
                        sideColor,
                        dripColor,
                    );
                    const cakeTopY = Number(blenderCake.userData.topY || 1.4);
                    const cakeToppingRadius = Number(
                        blenderCake.userData.toppingRadius || 1.1,
                    );
                    cakeGroup.add(blenderCake);
                    addToppings(
                        cakeGroup,
                        state.toppings,
                        shape,
                        cakeTopY,
                        cakeToppingRadius,
                        layers,
                        {
                            isCurrent: () => sequence === buildSequence,
                        },
                    );
                    const hasTopper = state.topper && state.topper !== "none";
                    if (state.message && !hasTopper) {
                        const message = makeTextSprite(
                            String(state.message).slice(0, 24),
                            state.textColor || "#7A3444",
                        );
                        message.position.set(0, cakeTopY + 0.075, 0.08);
                        message.rotation.x = -Math.PI / 2;
                        cakeGroup.add(message);
                    }
                    if (hasTopper) {
                        const topper = createTopper(
                            state.topper,
                            state.message,
                            state.textColor || "#7A3444",
                        );
                        topper.position.y = cakeTopY + 0.1;
                        cakeGroup.add(topper);
                    }
                    cakeGroup.scale.setScalar(1);
                    cakeGroup.position.y = 0.04;
                    const sizeScale = Number(
                        blenderCake.userData.sizeScale || 1,
                    );
                    floorShadow.scale.setScalar(
                        (1.08 + layers * 0.06) * sizeScale,
                    );
                    if (!userControlledView) setView("auto");
                })
                .catch(() => {
                    if (sequence === buildSequence) {
                        rebuildCake({ ...state, useProceduralFallback: true });
                    }
                });
            return;
        }

        let topRadius = baseRadius;
        for (let i = 0; i < layers; i += 1) {
            const radius = Math.max(0.58, baseRadius - i * 0.16);
            topRadius = radius;
            const tier = createRoundedTier(
                shape,
                radius,
                tierHeight,
                topColor,
                sideColor,
            );
            tier.position.y = i * tierHeight + tierHeight / 2;
            cakeGroup.add(tier);

            if (state.drip && state.drip !== "none") {
                const drips = createDrips(
                    shape,
                    radius,
                    tierHeight,
                    dripColor,
                    state.dripStyle || "curtain",
                    i + 1,
                );
                drips.position.y = tier.position.y;
                cakeGroup.add(drips);
            }
        }

        const topY = layers * tierHeight;
        addToppings(cakeGroup, state.toppings, shape, topY, topRadius, layers, {
            isCurrent: () => sequence === buildSequence,
        });

        const hasTopper = state.topper && state.topper !== "none";
        if (state.message && !hasTopper) {
            const message = makeTextSprite(
                String(state.message).slice(0, 24),
                state.textColor || "#7A3444",
            );
            message.position.set(0, topY + 0.075, 0.08);
            message.rotation.x = -Math.PI / 2;
            cakeGroup.add(message);
        }

        if (hasTopper) {
            const topper = createTopper(
                state.topper,
                state.message,
                state.textColor || "#7A3444",
            );
            topper.position.y = topY + 0.1;
            cakeGroup.add(topper);
        }

        cakeGroup.scale.setScalar(isImmersive ? 1.05 : 1);
        cakeGroup.position.y = isImmersive ? 0.04 : -0.28;
        floorShadow.scale.setScalar(
            ((isImmersive ? 1.08 : 0.96) + layers * 0.06) * sizeScale,
        );
    };

    const resize = () => {
        const width = Math.max(260, host.clientWidth || 320);
        const height = Math.max(300, host.clientHeight || 330);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height, false);
        updateCamera();
    };

    host.addEventListener("pointerdown", (event) => {
        isDragging = true;
        userControlledView = true;
        lastPointer = { x: event.clientX, y: event.clientY };
        host.setPointerCapture?.(event.pointerId);
        syncViewButtons(activeView === "auto" ? "auto" : activeView);
    });

    host.addEventListener("pointermove", (event) => {
        if (!isDragging) return;
        const dx = event.clientX - lastPointer.x;
        const dy = event.clientY - lastPointer.y;
        lastPointer = { x: event.clientX, y: event.clientY };
        viewYaw += dx * 0.012;
        viewPitch = Math.max(-0.8, Math.min(3.05, viewPitch + dy * 0.012));
        updateCamera();
    });

    const stopDragging = (event) => {
        if (!isDragging) return;
        isDragging = false;
        host.releasePointerCapture?.(event.pointerId);
    };

    host.addEventListener("pointerup", stopDragging);
    host.addEventListener("pointercancel", stopDragging);
    document.querySelectorAll("[data-cake-view]").forEach((button) => {
        button.addEventListener("click", () =>
            setView(button.dataset.cakeView, { userInitiated: true }),
        );
    });

    const animate = () => {
        rafId = requestAnimationFrame(animate);
        viewYaw += 0.012;
        turntableGroup.rotation.y = viewYaw;
        turntableGroup.rotation.z = 0;
        renderer.render(scene, camera);
    };

    window.BonbonCustomize3D = {
        update: rebuildCake,
        resize,
        setView,
    };

    window.addEventListener("bonbon-customize-3d:update", (event) => {
        rebuildCake(event.detail || window.__bonbonCustomize3DState || {});
    });
    window.addEventListener("resize", resize);

    setView("auto");
    resize();
    rebuildCake(state);
    if (isImmersive) {
        gsap.fromTo(
            turntableGroup.scale,
            { x: 0.72, y: 0.72, z: 0.72 },
            { x: 1.05, y: 1.05, z: 1.05, duration: 1.15, ease: "power3.out" },
        );
        gsap.fromTo(
            ".test-customize-panel",
            { autoAlpha: 0, y: 28, scale: 0.97 },
            {
                autoAlpha: 1,
                y: 0,
                scale: 1,
                duration: 0.8,
                stagger: 0.08,
                delay: 0.25,
                ease: "power3.out",
            },
        );
        gsap.fromTo(
            ".test-customize-hero",
            { autoAlpha: 0, y: -18 },
            {
                autoAlpha: 1,
                y: 0,
                duration: 0.8,
                delay: 0.1,
                ease: "power3.out",
            },
        );
    }
    animate();

    document.addEventListener("visibilitychange", () => {
        if (document.hidden && rafId) {
            cancelAnimationFrame(rafId);
            rafId = null;
            return;
        }

        if (!document.hidden && !rafId) {
            animate();
        }
    });
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initCustomizer3D);
} else {
    initCustomizer3D();
}
