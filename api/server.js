import express from 'express';
import cors from 'cors';
import bodyParser from 'body-parser';
import {
  generateAllMemoriesForCreature,
} from './generateMemories.js';


const app = express();
const PORT = 3000;

app.use(cors());
app.use(bodyParser.json());


app.post('/api/generateMemories', async (req, res) => {
    const { creature } = req.body;

    // Validate payload with better error messages
    if (!creature) {
      console.log('❌ No creature data provided');
      return res.status(400).json({ error: 'No creature data provided' });
    }
    
    const result = await generateAllMemoriesForCreature(creature);

    return res.status(200).json({ memories: result });
  });


app.listen(PORT, () => {
  console.log(`✅ Memory generation server running at http://localhost:${PORT}`);
});

