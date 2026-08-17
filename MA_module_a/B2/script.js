const titleElement = document.getElementById("title");
const topicElement = document.getElementById("topic");
const proVotesElement = document.getElementById("proVotes");
const conVotesElement = document.getElementById("conVotes");
const absVotesElement = document.getElementById("absVotes");
const proPercentageElement = document.getElementById("proPercentage");
const conPercentageElement = document.getElementById("conPercentage");
const absPercentageElement = document.getElementById("absPercentage");
const totalVotesElement = document.getElementById("totalVotes");
const proRow = document.getElementById("proRow");
const conRow = document.getElementById("conRow");
const absRow = document.getElementById("absRow");
const jsonFile = document.getElementById("jsonFile");

// Display voting results
function displayResults(data) {
    // Display title and topic
    titleElement.textContent = data.title;
    topicElement.textContent = data.topic;

    // Count votes
    let pro = 0;
    let con = 0;
    let abs = 0;
    data.votes.forEach(voter => {
        if (voter.vote === "pro") {
            pro++;
        }
        if (voter.vote === "con") {
            con++;
        }
        if (voter.vote === "abs") {
            abs++;
        }
    });

    // Total votes
    const total = pro + con + abs;

    // Calculate percentages
    const proPercentage = (pro / total) * 100;
    const conPercentage = (con / total) * 100;
    const absPercentage = (abs / total) * 100;

    // Display values
    proVotesElement.textContent = pro;
    conVotesElement.textContent = con;
    absVotesElement.textContent = abs;
    totalVotesElement.textContent = total;

    proPercentageElement.textContent = `${proPercentage.toFixed(1)}%`;

    conPercentageElement.textContent = `${conPercentage.toFixed(1)}%`;

    absPercentageElement.textContent = `${absPercentage.toFixed(1)}%`;

    proRow.classList.remove("majority");
    conRow.classList.remove("majority");
    absRow.classList.remove("majority");

    const highest = Math.max(pro, con, abs);
    if (pro === highest) {
        proRow.classList.add("majority");
    }
    if (con === highest) {
        conRow.classList.add("majority");
    }
    if (abs === highest) {
        absRow.classList.add("majority");
    }
}

// Load JSON through webserver
fetch("data.json")
    .then(response => response.json())
    .then(data => {
        displayResults(data);
    })
    .catch(() => {
        console.log("Select the JSON file manually.");
    });

// Load JSON
jsonFile.addEventListener("change", function () {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function (event) {
        const data = JSON.parse(event.target.result);
        displayResults(data);
    };
    reader.readAsText(file);
});