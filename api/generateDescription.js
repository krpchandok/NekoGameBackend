import { GoogleGenerativeAI } from "@google/generative-ai";
import dotenv from "dotenv";
dotenv.config();

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

export async function generateCreatureDescription(creature) {
  const model = genAI.getGenerativeModel({ model: "gemini-1.5-flash" });

  const traitsString = Array.isArray(creature.traits)
    ? creature.traits.join(", ")
    : creature.traits;

  const prompt = `Create a simple, cute description of a creature named ${creature.name}... with traits ${traitsString}`;

  try {
    const result = await model.generateContent(prompt);
    const response = await result.response;
    return response.text; 
  } catch (error) {
    console.warn(`Failed to generate description for ${creature.name}:`, error);
    return `${creature.name} is a wonderful creature with ${traitsString} traits who loves spending time in the garden.`;
  }
}
