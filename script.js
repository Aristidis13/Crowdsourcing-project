//------------------------------------------------------- adminMain.html Functions -----------------------------------------------------
/* This function confirms for deletion of the data. */
function dataDeletionConfirmation()
{
    if (confirm('Do you want to delete all data?'))
    {
        window.location.href='adminDelete.php';
    }
    else
    {
        userPreference="Data has not being deleted!";
         document.getElementById("confirmationMessage").innerHTML = userPreference;
    }
}

