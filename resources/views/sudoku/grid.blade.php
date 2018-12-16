@if(@$grid)
  <div class="text-center">
      <table class="table table-bordered sudoku-grid">
          @foreach($grid as $row)
              <tr scope="row">
                  @foreach($row as $cell)
                          @php
                              $class = "";
                              if ($cell->row%3 == 0 && $cell->row != 9)
                              {
                                  $class .= "border-bottom-2";
                              }
                              if ($cell->column%3 == 0 && $cell->column != 9)
                              {
                                  $class .= " border-right-2";
                              }
                          @endphp
                      <td class="{{$class}} sudoku-cell">
                          @if($cell->value == 0)
                              <table class="table table-borderless sudoku-pencil-marks">
                                  @foreach($cell->pencil_marks as $value => $mark)
                                      @if ($value !== 0)
                                          @if ($value%3 == 1) <tr> @endif
                                          <td class="padding-0">
                                              <small class="{{ $mark == 0 ? "invisible" : "" }}">{{$value}}</small>
                                          </td>
                                          @if ($value%3 == 0) </tr> @endif
                                      @endif
                                  @endforeach
                              </table>
                          @else
                            <span class="sudoku-cell-value">{{ $cell->value }}</span>
                          @endif
                      </td>
                  @endforeach
              </tr>
          @endforeach
      </table>
  </div>
@endif
