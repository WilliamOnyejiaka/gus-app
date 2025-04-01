import ImageApi from "./api/Image.js";

const uploadInput = document.querySelector('#upload-input') as HTMLInputElement;
const uploadBtn = document.querySelector('#upload-btn') as HTMLButtonElement;

uploadBtn.addEventListener('click', async e => {
    const files = uploadInput.files!;

    if (!files.length) {
        console.log("Please select a file");
    } else {
        const formData = new FormData();
        formData.append('image', files[0]);
        const result = await ImageApi.upload(formData);
        console.log(result);
    }

});