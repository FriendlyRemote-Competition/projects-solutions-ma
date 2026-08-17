const tabs = document.querySelectorAll(".tab");
const contents = document.querySelectorAll(".content");

tabs.forEach(tab => {
    tab.addEventListener("click", () => {
        // Remove active from all tabs
        tabs.forEach(tab => {
            tab.classList.remove("active");
        });
        // Remove active from all content
        contents.forEach(content => {
            content.classList.remove("active");
        });

        // Activate clicked tab
        tab.classList.add("active");

        // Find corresponding content
        const tabId = tab.dataset.tab;
        const content = document.getElementById(tabId);
        content.classList.add("active");
    });
});