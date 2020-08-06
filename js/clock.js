// Change these booleans to fit your needs
const displayDate = true;
const displayTime = true;

// To not load this script at all, update the settings in the web interface. Otherwise, you can manually disable it here, but it will still use resources loading the script.
let enabled = true;
let eClock = document.getElementById('clock');

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function updateClock() {
    while (enabled && eClock) {
        try {
            var date = new Date();
            if (displayDate && displayTime) {
                eClock.textContent = date.toLocaleString();
            } else if (displayDate && !displayTime) {
                eClock.textContent = date.toLocaleDateString();
            }
            else if (!displayDate && displayTime) {
                eClock.textContent = date.toLocaleTimeString();
            } else {
                enabled = false;
                break;
            }
            await sleep(500);
        } catch (e) {
            enabled = false;
            console.log("Issue with clock. Stopping clock. Error: " + e);
            break;
        }
    }
}

if (enabled) {
    updateClock();
}
