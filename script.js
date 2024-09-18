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
});
