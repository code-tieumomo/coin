import type { Grade } from "~/types/Grade";

export interface Subnet {
  id: number;
  name: string;
  icon: string;
  description: string;
  provider_embed_url: string;
  miner_embed_url: string;
  weight: number;
  needed: number;
  grades: Grade[];
  progress: number;
  is_completed: boolean;
}
