const gradientBox = document.getElementById("gradientBox");
const startColor = document.getElementById("startColor");
const endColor = document.getElementById("endColor");
const startColors = document.getElementById("startColors");
const endColors = document.getElementById("endColors");

// Update the gradient
function updateGradient() {
    const start = startColor.value;
    const end = endColor.value;
    gradientBox.style.background =
        `linear-gradient(to right, ${start}, ${end})`;
}

// Generate a random HEX color
function randomColor() {
    const characters = "0123456789ABCDEF";
    let color = "#";
    for (let i = 0; i < 6; i++) {
        color += characters[Math.floor(Math.random() * 16)];
    }
    return color;
}

// Create one color button
function createColorButton(container, input) {
    const color = randomColor();
    const button = document.createElement("button");
    button.type = "button";
    button.style.backgroundColor = color;
    button.title = color;
    button.addEventListener("click", function () {
        input.value = color;
        updateGradient();
    });
    container.appendChild(button);
}

// Create 12 random colors on each side
for (let i = 0; i < 12; i++) {
    createColorButton(startColors, startColor);
    createColorButton(endColors, endColor);
}

// Update when the user types manually
startColor.addEventListener("input", updateGradient);
endColor.addEventListener("input", updateGradient);

// Set initial gradient
updateGradient();