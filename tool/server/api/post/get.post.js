import { getQuery ,getBody } from "h3";
import { queryInclude } from "../../lib/firestore";

export default defineEventHandler(async (event) => {
  try {
    const query = getQuery(event);
    const body = await readBody(event);
    const docs = await queryInclude(body.col,'post_id',body.listId);

    
    return { result: docs };
  } catch (error) {
    return { result: [], error: error.message };
  }
});
