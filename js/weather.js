// AJAX Weather
// Options are limited for this API, I suggest implementing openweather if you're willing to pay.
// https://www.metaweather.com/api/

// Get elements and variables
const weatherElement = document.getElementById('weather');
const woeId = document.getElementById('weatherWoeId').innerText;
const weatherProxy = document.getElementById('weatherProxy').innerText;
const weatherMeasurement = Number(document.getElementById('weatherMeasurement').innerText);

function celsiusToFahrenheit(c) {
    let f = null;
    f = (c * (9/5)) + 32;
    return Math.round(f);
}

async function getWeatherAW(proxy, woeId) {
    try {
        const result = await fetch(`${proxy}https://www.metaweather.com/api/location/${woeId}`);
        const data = await result.json();

        // Celsius
        let todayHi =  data.consolidated_weather[0].max_temp;
        let todayLo = data.consolidated_weather[0].min_temp;
        let todayWeather = data.consolidated_weather[0].weather_state_name;
        
        let tomorrowHi = data.consolidated_weather[1].max_temp;
        let tomorrowLo = data.consolidated_weather[1].max_temp;
        let tomorrowWeather = data.consolidated_weather[1].weather_state_name;

        // Convert to Fahrenheit if set
        if (weatherMeasurement === 1) {
            todayHi = celsiusToFahrenheit(todayHi);
            todayLo = celsiusToFahrenheit(todayLo);
            tomorrowHi = celsiusToFahrenheit(tomorrowHi);
            tomorrowLo = celsiusToFahrenheit(tomorrowLo);
        } else {
            // avoid rounding before celsius calculations
            todayHi = Math.round(todayHi);
            todayLo = Math.round(todayLo);
            tomorrowHi = Math.round(tomorrowHi);
            tomorrowLo = Math.round(tomorrowLo);
        }
        
        weatherElement.innerHTML = `${data.parent.title} <br><br>
        Today:<br>
        High: ${todayHi}<br>
        Low: ${todayLo}<br>
        ${todayWeather}<br><br>
        Tomorrow: <br>
        High: ${tomorrowHi}<br>
        Low: ${tomorrowLo}<br>
        ${tomorrowWeather}`;

        return data;
    } catch (error) {
        console.log('Error in weather.js getWeatherAW.');
    }
}

if (woeId) {
    let weatherData = getWeatherAW(weatherProxy, woeId).then(result => console.log(result));
} else {
    console.log("Weather.js couldn't find the woeId in the DOM!");
}

