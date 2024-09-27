document.addEventListener("DOMContentLoaded", function() {
    let balance = 0;

    const balanceDisplay = document.getElementById("balance");
    const amountInput = document.getElementById("amountInput");
    const message = document.getElementById("message");

    const depositButton = document.getElementById("depositButton");
    const withdrawButton = document.getElementById("withdrawButton");

    // Function to update balance display
    function updateBalance() {
        balanceDisplay.textContent = balance.toFixed(2);
    }

    // Function to show message
    function showMessage(text, isError = false) {
        message.textContent = text;
        message.style.color = isError ? "red" : "green";
    }

    // Event Listener for Deposit
    depositButton.addEventListener("click", function() {
        const amount = parseFloat(amountInput.value);
        if (isNaN(amount) || amount <= 0) {
            showMessage("Please enter a valid amount!", true);
            return;
        }

        balance += amount;
        updateBalance();
        showMessage(`Deposited $${amount.toFixed(2)} successfully!`);
        amountInput.value = "";
    });

    // Event Listener for Withdraw
    withdrawButton.addEventListener("click", function() {
        const amount = parseFloat(amountInput.value);
        if (isNaN(amount) || amount <= 0) {
            showMessage("Please enter a valid amount!", true);
            return;
        }

        if (amount > balance) {
            showMessage("Insufficient funds!", true);
            return;
        }

        balance -= amount;
        updateBalance();
        showMessage(`Withdrew $${amount.toFixed(2)} successfully!`);
        amountInput.value = "";
    });
    const historyList = document.getElementById("historyList");

function updateHistory(transactionType, amount) {
    const listItem = document.createElement("li");
    listItem.textContent = `${transactionType}: $${amount.toFixed(2)}`;
    historyList.appendChild(listItem);
}

depositButton.addEventListener("click", function() {
    const amount = parseFloat(amountInput.value);
    if (isNaN(amount) || amount <= 0) {
        showMessage("Please enter a valid amount!", true);
        return;
    }

    balance += amount;
    updateBalance();
    showMessage(`Deposited $${amount.toFixed(2)} successfully!`);
    updateHistory('Deposit', amount); // Log to history
    amountInput.value = "";
});

withdrawButton.addEventListener("click", function() {
    const amount = parseFloat(amountInput.value);
    if (isNaN(amount) || amount <= 0) {
        showMessage("Please enter a valid amount!", true);
        return;
    }

    if (amount > balance) {
        showMessage("Insufficient funds!", true);
        return;
    }

    balance -= amount;
    updateBalance();
    showMessage(`Withdrew $${amount.toFixed(2)} successfully!`);
    updateHistory('Withdraw', amount); // Log to history
    amountInput.value = "";
});
// Initial balance
let balance = 0;

// DOM Elements
const balanceDisplay = document.getElementById("balance");
const amountInput = document.getElementById("amountInput");
const message = document.getElementById("message");
const historyList = document.getElementById("historyList");
const accountHolderInput = document.getElementById("accountHolderInput");
const saveAccountHolderButton = document.getElementById("saveAccountHolderButton");
const accountHolderName = document.getElementById("accountHolderName");
const depositButton = document.getElementById("depositButton");
const withdrawButton = document.getElementById("withdrawButton");
const resetButton = document.getElementById("resetButton");

// Function to update balance display
function updateBalance() {
    balanceDisplay.textContent = balance.toFixed(2);
}

// Function to show message
function showMessage(text, isError = false) {
    message.textContent = text;
    message.style.color = isError ? "red" : "green";
}

// Input validation function
function isValidAmount(amount) {
    if (isNaN(amount) || amount <= 0 || amount > 1000000) {
        showMessage("Please enter a valid amount between $0.01 and $1,000,000.", true);
        return false;
    }
    return true;
}

// Update transaction history
function updateHistory(transactionType, amount) {
    const listItem = document.createElement("li");
    listItem.textContent = `${transactionType}: $${amount.toFixed(2)}`;
    historyList.appendChild(listItem);
}

// Event listener for deposit
depositButton.addEventListener("click", function() {
    const amount = parseFloat(amountInput.value);
    if (!isValidAmount(amount)) return;

    balance += amount;
    updateBalance();
    showMessage(`Deposited $${amount.toFixed(2)} successfully!`);
    updateHistory('Deposit', amount);
    amountInput.value = "";
});

// Event listener for withdrawal
withdrawButton.addEventListener("click", function() {
    const amount = parseFloat(amountInput.value);
    if (!isValidAmount(amount)) return;

    if (amount > balance) {
        showMessage("Insufficient funds!", true);
        return;
    }

    balance -= amount;
    updateBalance();
    showMessage(`Withdrew $${amount.toFixed(2)} successfully!`);
    updateHistory('Withdraw', amount);
    amountInput.value = "";
});

// Event listener for reset button
resetButton.addEventListener("click", function() {
    balance = 0;
    updateBalance();
    historyList.innerHTML = ""; // Clear transaction history
    showMessage("Balance and transaction history have been reset.");
});

// Save account holder name to local storage
saveAccountHolderButton.addEventListener("click", function() {
    const name = accountHolderInput.value.trim();
    if (name === "") {
        showMessage("Please enter a valid name.", true);
        return;
    }

    localStorage.setItem("accountHolder", name);
    accountHolderName.textContent = name;
    showMessage(`Account holder saved as ${name}.`);
    accountHolderInput.value = "";
});

// Load account holder name from local storage on page load
function loadAccountHolder() {
    const storedName = localStorage.getItem("accountHolder");
    if (storedName) {
        accountHolderName.textContent = storedName;
    }
}

// Initialize page with stored account holder
loadAccountHolder();

});
