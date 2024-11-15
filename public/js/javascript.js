
function confirmDelete(taskId)
{
    var isConfirmed = confirm("Weet je zeker dat je dit wilt verwijderen?");

    if (isConfirmed)
    {
        window.location.href = "/task/delete?taskId=" + taskId;
    }
    else
    {
        alert("Verwijdering geannuleerd.");
    }
}