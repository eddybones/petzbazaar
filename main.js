function confirmation(message) {
    if(confirm(message)) {
        return true;
    } else {
        return false;
    }
}

function handleCheck(event) {
    // Get whether the checkbox was checked
    let checked = event.target.checked;
    // Get the value attribute of the checkbox
    let messageId = event.target.value;
    // Get a reference to this DOM element
    let hiddenInput = document.getElementById("deleteMessages");

    // Create a blank array
    let idsToDelete = [];
    // If our hidden input isn't blank, get it's value as an array
    if(hiddenInput.value != "") {
        idsToDelete = hiddenInput.value.split(",");
    }

    // Append or remove from the array
    if(checked) {
        idsToDelete.push(messageId);
    } else {
        let index = idsToDelete.indexOf(messageId);
        idsToDelete.splice(index, 1);
    }

    // Rewrite the value attribute in the DOM
    hiddenInput.value = idsToDelete.join(",");
}

function toggleAuctionType(type) {
    let silentContainer = document.getElementById("silentContainer");
    let standardContainer = document.getElementById("standardContainer");

    if(type == 'silent') {
        silentContainer.style.display = 'block';
        standardContainer.style.display = 'none';
    }

    if(type == 'standard') {
        silentContainer.style.display = 'none';
        standardContainer.style.display = 'block';
    }
}
