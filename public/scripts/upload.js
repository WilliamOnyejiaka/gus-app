var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import ImageApi from "./api/Image.js";
const uploadInput = document.querySelector('#upload-input');
const uploadBtn = document.querySelector('#upload-btn');
uploadBtn.addEventListener('click', (e) => __awaiter(void 0, void 0, void 0, function* () {
    const files = uploadInput.files;
    if (!files.length) {
        console.log("Please select a file");
    }
    else {
        const formData = new FormData();
        formData.append('image', files[0]);
        const result = yield ImageApi.upload(formData);
        console.log(result);
    }
}));
