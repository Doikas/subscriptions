document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.querySelector('#custom-service-field');
    const expiredDateInput = document.querySelector('#custom-date-field');

    if (serviceSelect) {
        serviceSelect.addEventListener('change', async () => {
            try {
                const selectedServiceId = serviceSelect.value;
                const response = await fetch(`get-expiration/${selectedServiceId}`);
                const { status, headers } = response;
                console.log(response);
                console.log(status);
                console.log(headers.get('Content-Type'));

                if (response.ok) {
                    const data = await response.json();

                    if (data.expiration) {
                        const currentDate = new Date();
                        const yearsToAdd = parseInt(data.expiration);
                        const newExpiredDate = addYears(currentDate, yearsToAdd);
                        const formattedNewExpiredDate = formatDate(newExpiredDate);

                        expiredDateInput.value = formattedNewExpiredDate;
                    }
                } else {
                    console.error(`Fetch error: ${status}`);
                }
            } catch (error) {
                console.error('An unexpected error occurred:', error);
            }
        });
    } else {
        console.log('serviceSelect element not found. Check your selector.');
    }
});


// Function to add a specified number of years to a date
function addYears(date, yearsToAdd) {
    const newDate = new Date(date);
    newDate.setFullYear(newDate.getFullYear() + yearsToAdd);
    return newDate;
}

// Function to format a date as "YYYY-MM-DD"
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

const button = document.querySelector('.send-status-button');
button.addEventListener('click', function() {
    const subscriptionId = button.getAttribute('data-subscription-id');
    
    const queryParameters = window.location.search.substr(1);
    const urlParams = new URLSearchParams(queryParameters);
    
    const customerFilter = urlParams.get('customer');
    const domainFilter = urlParams.get('domain');
    const emailFilter = urlParams.get('email');
    
    // Make an AJAX request with the captured values
    fetch(`/send-status-email/${subscriptionId}?customer=${customerFilter}&domain=${domainFilter}&email=${emailFilter}`, {
        method: 'POST', // Adjust the HTTP method as needed
    })
    .then(response => {
        // Handle the response from the server
        if (response.ok) {
            // The request was successful
        } else {
            // Handle errors
        }
    })
    .catch(error => {
        // Handle network or other errors
    });
});