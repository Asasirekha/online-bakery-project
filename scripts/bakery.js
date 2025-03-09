// document.getElementById('place').addEventListener('change', function() {
//     // Hide all detail boxes
//     document.querySelectorAll('#details > div').forEach(function(detail) {
//         detail.style.display = 'none';
//     });

//     // Show the selected detail box
//     var selectedValue = this.value;
//     if (selectedValue) {
//         var detailBox = document.getElementById('detail-' + selectedValue);
//         if (detailBox) {
//             detailBox.style.display = 'block';
//             this.style.display=
//             document.getElementById('details').style.display = 'inline-block';
//         }
//     }
// });

document.getElementById('place').addEventListener('change', function() {
    var detailsContainer = document.getElementById('details');
    var selectedValue = this.value;

    // Hide all detail boxes
    document.querySelectorAll('#details > div').forEach(function(detail) {
        detail.style.display = 'none';
    });

    // Show the selected detail box
    if (selectedValue) {
        var detailBox = document.getElementById('detail-' + selectedValue);
        if (detailBox) {
            detailBox.style.display = 'block';
            detailsContainer.style.display = 'inline-block';
            // document.body.style.backgroundColor = '#f0f0f0'; // Change to light color
        }
    } else {
        detailsContainer.style.display = 'none';
        document.body.style.backgroundColor = ''; // Reset to default
    }
});
let crossMark = document.querySelector("#cross");
let container=document.querySelector("#container");

crossMark.addEventListener('click',function(){
    container.style.display = 'none';
});