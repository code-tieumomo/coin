import type { Subnet } from "~/types/Subnet";

export interface Assignment {
  id: number;
  title: string;
  description: string;
  start_date: string;
  end_date: string;
  is_public: number;
  subnets: Subnet[];
}
