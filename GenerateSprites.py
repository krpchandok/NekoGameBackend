from diffusers import DiffusionPipeline, StableDiffusionXLImg2ImgPipeline
import torch
import os

# Create output directories if they don't exist
os.makedirs("images", exist_ok=True)
os.makedirs("images_refined", exist_ok=True)

# Load the base SDXL model
pipe = DiffusionPipeline.from_pretrained(
    "stabilityai/stable-diffusion-xl-base-1.0",
    torch_dtype=torch.float16,
    variant="fp16"
).to("cuda")

# Load your fine-tuned LoRA adapter
pipe.load_lora_weights("model/", weight_name="pytorch_lora_weights.safetensors")

# Load the SDXL refiner model
refiner = StableDiffusionXLImg2ImgPipeline.from_pretrained(
    "stabilityai/stable-diffusion-xl-refiner-1.0",
    torch_dtype=torch.float16,
    variant="fp16"
).to("cuda")

# Define prompts
prompt = "sprite of sks warrior cat"
negative_prompt = "photo, human, realistic"

# Generate and refine 10 variations
for seed in range(10):
    generator = torch.Generator("cuda").manual_seed(seed)

    # Generate base image
    output = pipe(
        prompt=prompt,
        negative_prompt=negative_prompt,
        guidance_scale=15,
        num_inference_steps=25,
        generator=generator
    )
    image = output.images[0]
    image.save(f"images/{seed}.png")

    # Refine image
    refined_output = refiner(
        prompt=prompt,
        image=image,
        num_inference_steps=25,
        guidance_scale=15,
        generator=generator
    )
    refined_image = refined_output.images[0]
    refined_image.save(f"images_refined/{seed}.png")
