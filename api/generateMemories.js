import { GoogleGenerativeAI } from "@google/generative-ai";
import dotenv from "dotenv";
import { INTERACTIVITY_SCORE } from '../constants/index.js';
dotenv.config();

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

const interactivityScores = INTERACTIVITY_SCORE;

export async function generateMemory(creature, interactivityScore) {
  const model = genAI.getGenerativeModel({ model: "gemini-1.5-flash" });
  const traitsString = Array.isArray(creature.traits) ? creature.traits.join(" and ") : creature.traits;
  const prompt = `You are writing a memory from the perspective of ${creature.name} who is ${traitsString}... considering you have interacted with them 
  ${interactivityScore} times. Use that number to base the intimacy (not weird please these are cute pet like creatures) of the memory`;

    const result = await model.generateContent(prompt);
    const response = await result.response;
    const memory = response.text().trim();
    return {
      content: memory,
      unlockedAt: interactivityScore,
    }}

export async function generateAllMemoriesForCreature(creature) {
  let memories = [];

  for (const score in interactivityScores) {
      const memory = await generateMemory(creature, parseInt(score));
      if (memory?.content) {
        memories = [...memories, memory]
      }
      await new Promise(resolve => setTimeout(resolve, 100));
    }

  return memories;
}
