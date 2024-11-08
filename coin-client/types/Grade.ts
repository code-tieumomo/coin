export interface Grade {
  id: number;
  user_id: number;
  subnet_id: number;
  grade: string;
  comment: string | null;
}
