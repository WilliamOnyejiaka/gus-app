
export default class ImageApi {

    private static readonly baseUrl: string = '/public/image';

    public static async upload(formData: FormData) {
        try {
            const response = await fetch(ImageApi.baseUrl + '/upload', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            return {
                error: false,
                result
            };
        } catch (error) {
            console.error(error);
            return {
                error: true,
                result: null
            };
        }
    }
}