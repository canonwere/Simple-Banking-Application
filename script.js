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
/* Account Holder Section */
.account-section {
    margin-bottom: 20px;
}

#accountHolderInput {
    padding: 10px;
    margin-bottom: 10px;
    width: 80%;
    font-size: 16px;
}

/* Reset Button Style */
button#resetButton {
    background-color: #ff9800;
    margin-top: 10px;
}

/* Transaction History Style */
.transaction-history {
    margin-top: 20px;
}

.transaction-history ul {
    list-style-type: none;
    padding: 0;
}

.transaction-history li {
    margin: 5px 0;
    font-size: 16px;
}

});
