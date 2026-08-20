const roomsData = [
    {
        number: "01 / 04",
        title: "Deluxe River Room",
        desc: "A calm river-facing room shaped by warm timber, cream linen and morning light.",
        space: "42 m²",
        guests: "2",
        bed: "1 king bed",
        price: "From CNY 2,380 / night",
        image: "./media_files/images/room-deluxe-river.webp"
    },
    {
        number: "02 / 04",
        title: "Art Deco King Room",
        desc: "Geometric detailing and rich, muted colour reinterpret Shanghai glamour for a modern stay.",
        space: "48 m²",
        guests: "2",
        bed: "1 king bed",
        price: "From CNY 2,680 / night",
        image: "./media_files/images/room-art-deco-king.webp"
    },
    {
        number: "03 / 04",
        title: "Suzhou Suite",
        desc: "A residential suite with separate living space and wide views over the moving river.",
        space: "68 m²",
        guests: "3",
        bed: "1 king bed",
        price: "From CNY 3,680 / night",
        image: "./media_files/images/room-suzhou-suite.webp"
    },
    {
        number: "04 / 04",
        title: "Bund Signature Suite",
        desc: "Our generous corner suite frames Shanghai after dark with cinematic views and quiet privacy.",
        space: "92 m²",
        guests: "4",
        bed: "1 king bed",
        price: "From CNY 5,280 / night",
        image: "./media_files/images/room-bund-signature.webp"
    }
];

function switchRoom(index) {
    const room = roomsData[index];
    document.getElementById('roomNumber').innerText = room.number;
    document.getElementById('roomTitle').innerText = room.title;
    document.getElementById('roomDesc').innerText = room.desc;
    document.getElementById('roomSpace').innerText = room.space;
    document.getElementById('roomGuests').innerText = room.guests;
    document.getElementById('roomBed').innerText = room.bed;
    document.getElementById('roomPrice').innerText = room.price;

    const imgEl = document.getElementById('roomMainImage');
    imgEl.style.backgroundImage = `url('${room.image}')`;

    const buttons = document.querySelectorAll('.room-tab-btn');
    buttons.forEach((btn, i) => {
        if (i === index) btn.classList.add('active');
        else btn.classList.remove('active');
    });
}


const mapmark1 = document.getElementById('mapmark1');
const mapmark2 = document.getElementById('mapmark2');
const mapmark3 = document.getElementById('mapmark3');

const nearbyItem1 = document.getElementById('nearby-item-1');
const nearbyItem2 = document.getElementById('nearby-item-2');
const nearbyItem3 = document.getElementById('nearby-item-3');

mapmark1.addEventListener('mouseover', () => {
    nearbyItem1.classList.add('highlighted');
});
mapmark1.addEventListener('mouseout', () => {
    nearbyItem1.classList.remove('highlighted');
});

mapmark2.addEventListener('mouseover', () => {
    nearbyItem2.classList.add('highlighted');
});
mapmark2.addEventListener('mouseout', () => {
    nearbyItem2.classList.remove('highlighted');
});

mapmark3.addEventListener('mouseover', () => {
    nearbyItem3.classList.add('highlighted');
});
mapmark3.addEventListener('mouseout', () => {
    nearbyItem3.classList.remove('highlighted');
});